<?php
declare(strict_types=1);

namespace FunTask\Command;

use FunTask\Domain\CategoryTreeLoader;
use FunTask\Visitor\TreeWalker;
use FunTask\Visitor\MenuBuilderVisitor;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MenuBuilderCommand extends Command
{
    protected static $defaultName = 'app:menu-builder';

    protected function configure(): void
    {
        $this
            ->setDescription('Builds public menu based on tags and flags.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to categories.json', 'data/categories.json')
            ->addOption('adult', null, InputOption::VALUE_REQUIRED, 'adult (0/1/true/false)', '0')
            ->addOption('region', null, InputOption::VALUE_REQUIRED, 'region (kg|ru)', 'kg')
            ->addOption('staff', null, InputOption::VALUE_REQUIRED, 'staff (0/1/true/false)', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }
        $adultRaw = $input->getOption('adult');

        if (!is_string($adultRaw) && $adultRaw !== null) {
            throw new \InvalidArgumentException('Option "adult" must be a scalar value');
        }
        $adult = $this->parseBool((string) $adultRaw);

        $staffRaw = $input->getOption('staff');
        if (!is_string($staffRaw) && $staffRaw !== null) {
            throw new \InvalidArgumentException('Option "staff" must be a scalar value');
        }
        $staff = $this->parseBool((string) $staffRaw);

        $region = $input->getOption('region');
        if (!is_string($region)) {
            throw new \InvalidArgumentException('Option "region" must be a string');
        }

        if (!in_array($region, ['kg', 'ru'], true)) {
            $output->writeln('<error>Invalid --region. Use kg or ru.</error>');
            return Command::FAILURE;
        }

        $root = (new CategoryTreeLoader())->load($path);

        $visitor = new MenuBuilderVisitor($adult, $region, $staff);
        (new TreeWalker())->walk($root, $visitor);

        foreach ($visitor->resultLines() as $line) {
            $output->writeln($line);
        }

        return Command::SUCCESS;
    }

    private function parseBool(string $raw): bool
    {
        $raw = strtolower(trim($raw));

        if (in_array($raw, ['1','true','yes','y','on'], true)) return true;
        if (in_array($raw, ['0','false','no','n','off'], true)) return false;

        throw new \InvalidArgumentException('Invalid boolean value: ' . $raw);
    }
}