<?php
declare(strict_types = 1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

final class SearchIndexExportVisitor implements CategoryVisitor
{
    /** @var bool */
    private bool $staff;

    /** @var array<int, array<string, mixed>> */
    private array $documents = [];

    public function __construct(bool $staff)
    {
        $this->staff = $staff;
    }

    public function enter(Category $node): bool
    {
        //чтобы не выводить если нет серчбл
        if (!$node ->hasTagType('searchable')) {
            return true;
        }

        //Чтобы убрать хиден
        if ($node->hasTagType('hidden'))
        {
            return true;
        }

        //Не выводим стафф онли если у нас стафф не тру
        if (!$this->staff && $node->hasTag('restricted', 'staff-only'))
        {
            return true;
        }

        $adult = $node->hasTag('restricted', '18plus');
        $regions = $node->tagValues('region');

        $this->documents[] = [
            'id' => $node->id(),
            'name' => $node->name(),
            'adult' => $adult,
            'regions' => $regions,
        ];

        return true;
    }

    public function leave(Category $node): void {}

    /** @return array<int, array<string, mixed>> */
    public function results(): array
    {
        return $this->documents;
    }
}