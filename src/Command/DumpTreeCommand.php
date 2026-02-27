<?php
declare(strict_types=1);

namespace FunTask\Command;

use FunTask\Domain\Category;
use FunTask\Domain\CategoryTreeLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DumpTreeCommand extends Command
{
    protected static $defaultName = 'dumpTree';

    protected function configure(): void
    {
        $this
            ->setDescription('Loads categories from JSON and prints the tree.')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to categories.json',
                'data/categories.json'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if(!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');}

        $loader = new CategoryTreeLoader();
        $root = $loader->load($path);

        $this->printTree($root, 0, $output);

        return Command::SUCCESS;
    }

    private function printTree(Category $node, int $depth, OutputInterface $output): void
    {
        $output->writeln(str_repeat('  ', $depth) . $node->name() . ' (' . $node->id() . ')');

        foreach ($node->children() as $child) {
            $this->printTree($child, $depth + 1, $output);
        }
    }
}
