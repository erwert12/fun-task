<?php
declare(strict_types=1);

namespace FunTask\Command;

use FunTask\Domain\CategoryTreeLoader;
use FunTask\Visitor\VisibilityAuditVisitor;
use FunTask\Visitor\TreeWalker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class VisibilityAuditCommand extends Command
{
    protected static $defaultName = 'app:visibility-audit';

    protected function configure(): void
    {
        $this
            ->setDescription('Audits visibility of categories based on tags and flags.')
            ->addArgument('path', InputArgument::OPTIONAL, 'Path to categories.json', 'data/categories.json');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if (!is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        try {
            $root = (new CategoryTreeLoader())->load($path);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $visitor = new VisibilityAuditVisitor();
        (new TreeWalker())->walk($root, $visitor);

        foreach ($visitor->resultLines() as $line) {
            $output->writeln($line);
        }

        return Command::SUCCESS;
    }
}