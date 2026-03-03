<?php

declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

abstract class VisibilityAuditVisitor implements CategoryVisitor
{
    /** @var array<int, array<string, mixed>> */
    private array $problems = [];

    public function visitCategory(Category $category): void
    {
        $issues = [];

        if ($category->hasTagType('hidden')) {
            $issues[] = 'Категория скрыта';
        }
        if ($category->hasTagType('promo') && !$category->hasTag('searchable')) {
            $issues[] = "Промо без поиска";
        }
        if ($category->hasTagType('menu') && $category->hasTag('restricted') ) {
            $issues[] = "Restricted в меню";
        }

        if ($issues !== []) {
            $this->problems[] = [
                'id' => $category->id(),
                'name' => $category->name(),
                'issues' => $issues,
            ];
        }

        foreach ($category -> children() as $child) {
            $this->visitCategory($child);
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function getProblems(): array
    {
        return $this->problems;
    }
}