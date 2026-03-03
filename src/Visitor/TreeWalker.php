<?php
declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;
use FunTask\Visitor\CategoryVisitor;

final class TreeWalker
{
    public function walk(Category $node, CategoryVisitor $visitor): void
    {
        $goDeeper = $visitor->enter($node);

        if ($goDeeper) {
            foreach ($node->children() as $child) {
                $this->walk($child, $visitor);
            }
        }

        $visitor->leave($node);
    }
}