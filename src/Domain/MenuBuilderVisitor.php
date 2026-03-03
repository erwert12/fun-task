<?php
declare(strict_types=1);

namespace FunTask\Domain;

final class MenuBuilderVisitor implements CategoryVisitor
{
    /** @var string[] */
    private array $menu = [];
    private bool $adult;
    private string $region;
    private bool $staff;

    public function __construct(bool $adult, string $region, bool $staff)
    {
        $this->adult = $adult;
        $this->region = $region;
        $this->staff = $staff;
    }

    public function visit(Category $category): void
    {
        // фильтры
        if ($category->hasTag('hidden')) {
            return;
        }
        if (!$this->staff && $category->hasTag('restricted', 'staff-only')) {
            return;
        }
        if (!$this->adult && $category->hasTag('restricted', '18plus')) {
            return;
        }
        if ($this->region === 'kg' && $category->hasTag('region', 'ru')) {
            return;
        }
        if ($this->region === 'ru' && $category->hasTag('region', 'kg')) {
            return;
        }

        if ($category->hasTagType('menu')) {
            $this->menu[] = $category->name();
        }

        foreach ($category->children() as $child) {
            $this->visit($child);
        }
    }

    /** @return string[] */
    public function getMenu(): array
    {
        return $this->menu;
    }
}