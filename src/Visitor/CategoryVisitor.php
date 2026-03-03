<?php
declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

interface CategoryVisitor
{
    /**
     * Суть в том чтобы возвращать фолс когда не надо идти в детей, а не делать это в самом визиторе. Тогда можно будет использовать один и тот же визитор для разных задач, а не плодить кучу классов с разной логикой обхода.
     */
    public function enter(Category $node): bool;

    public function leave(Category $node): void;
}
