<?php
declare(strict_types=1);

namespace FunTask\Command;

use FunTask\Domain\CategoryTreeLoader;
use FunTask\Visitor\SearchIndexExportVisitor;
use FunTask\Visitor\TreeWalker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SearchIndexExportCommand extends Command
{
    protected static $defaultName = 'app:search-index-export';

    protected function configure(): void
    {
        $this
            ->setDescription('Exports search index data based on categories.json.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to categories.json', 'data/categories.json')
            ->addoption('staff', null, InputOption::VALUE_REQUIRED, 'staff (0/1/true/false)', '0');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $path = $input->getArgument('path');

        if(!is_string($path))
        {
            throw new \InvalidArgumentException('Path must be a string');
        }

        $staffRaw = $input->getOption('staff');
        if (!is_string($staffRaw) && $staffRaw !== null) {
            throw new \InvalidArgumentException('Option "staff" must be a scalar value');
        }
        $staff = $this->parseBool((string) $staffRaw);

        $root = (new CategoryTreeLoader())->load($path);

        $visitor = new SearchIndexExportVisitor($staff);
        (new TreeWalker())->walk($root, $visitor);

        $json = json_encode($visitor->results(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            $output->writeln('<error>Failed to encode JSON.</error>');
            return Command::FAILURE;
        }

        $outputFile = 'search_index_export.json';
        if (file_put_contents($outputFile, $json) === false)
        {
            $output->writeln('<error>Failed to write to file</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Export written to' . $outputFile . '</info>');
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
