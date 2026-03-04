<?php
declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

final class MenuBuilderVisitor implements CategoryVisitor
{
    /** @var bool */
    private bool $adult;

    /** @var string */
    private string $region;

    /** @var bool */
    private bool $staff;

    /** @var int */
    private int $depth = 0;

    /** @var string[] */
    private array $lines = [];

    /**
     * @param string $region expected 'kg'|'ru'
     */
    public function __construct(bool $adult, string $region, bool $staff)
    {
        $this->adult = $adult;
        $this->region = $region;
        $this->staff = $staff;
    }

    public function enter(Category $node): bool
    {
        if (!$this->isAllowed($node)) {
            return false; // режем всю ветку
        }

        // печатаем только "menu" узлы
        if ($node->hasTagType('menu')) {
            $this->lines[] = str_repeat('  ', $this->depth) . $node->name();
            $this->depth++;
            return true;
        }

        // узел сам не menu, но детей можем обходить
        return true;
    }

    public function leave(Category $node): void
    {
        // depth уменьшаем только если в enter мы его увеличили (т.е. узел был menu)
        if ($this->isAllowed($node) && $node->hasTagType('menu')) {
            $this->depth--;
        }
    }

    /** @return string[] */
    public function resultLines(): array
    {
        return $this->lines;
    }

    private function isAllowed(Category $node): bool
    {
        // hidden => никогда
        if ($node->hasTagType('hidden')) {
            return false;
        }

        if (!$this->staff && $node->hasTag('restricted', 'staff-only')) {
            return false;
        }

        // 18plus => только если adult=true
        if (!$this->adult && $node->hasTag('restricted', '18plus')) {
            return false;
        }

        // region filter: если у узла есть region:* то должен совпасть
        $regions = $node->tagValues('region');
        if ($regions !== [] && !in_array($this->region, $regions, true)) {
            return false;
        }

        return true;
    }
}