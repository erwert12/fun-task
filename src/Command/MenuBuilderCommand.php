<?php
declare(strict_types=1);

namespace FunTask\Command;

use FunTask\Domain\CategoryTreeLoader;
use FunTask\Domain\MenuBuilderVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MenuBuilderCommand extends Command
{
    protected static $defaultName = 'menuBuilder';

    protected function configure(): void
    {
        $this
            ->setDescription('Builds public menu from categories.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to categories.json')
            ->addOption('adult', null, InputOption::VALUE_REQUIRED, 'Adult mode', 'false')
            ->addOption('region', null, InputOption::VALUE_REQUIRED, 'Region', 'ru')
            ->addOption('staff', null, InputOption::VALUE_REQUIRED, 'Staff mode', 'false');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = (string)$input->getArgument('path');
        $adult = $input->getOption('adult') === 'true';
        $region = (string)$input->getOption('region');
        $staff = $input->getOption('staff') === 'true';

        $loader = new CategoryTreeLoader();
        $root = $loader->load($path);

        $visitor = new MenuBuilderVisitor($adult, $region, $staff);
        $visitor->visit($root);

        foreach ($visitor->getMenu() as $item) {
            $output->writeln('- ' . $item);
        }

        return Command::SUCCESS;
    }
}