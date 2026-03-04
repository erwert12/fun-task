<?php
declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

final class VisibilityAuditVisitor implements CategoryVisitor
{
    /** @var int */
    private int $depth = 0;

    /** @var string[] */
    private array $lines = [];

    /** @var array<string> */
    private array $openMenuIds = [];

    public function enter(Category $node): bool
    {
        $issues = [];

        if ($node->hasTagType('hidden')) {
            $issues[] = 'Скрыт';
            $this->lines[] = str_repeat('.../', $this->depth) . $node->name() . '[' . $node->id() . ']' . ' — (' . implode(', ', $issues) . ')';
            return false; // не спускаемся в детей hidden узлов
        }

        if ($node->hasTagType('promo') && !$node->hasTagType('searchable')) {
            $issues[] = 'Промо без searchable';
        }

        if ($node->hasTagType('menu') && $node->hasTagType('restricted')) {
            $issues[] = 'Меню с restricted';
        }

        if ($issues !== []) {
            $this->lines[] = str_repeat('.../', $this->depth) . $node->name() . '[' . $node->id() . ']' . ' — (' . implode(', ', $issues) . ')';
        }

        if ($node->hasTagType('menu')) {
            $this->depth++;
            $this->openMenuIds[] = $node->id();
        }

        return true;
    }

    public function leave(Category $node): void
    {
        if ($node->hasTagType('menu') && in_array($node->id(), $this->openMenuIds, true)) {
            $this->depth--;
            array_pop($this->openMenuIds);
        }
    }

    /** @return string[] */
    public function resultLines(): array
    {
        return $this->lines;
    }
}