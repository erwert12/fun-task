<?php
declare(strict_types=1);

namespace FunTask\Visitor;

use FunTask\Domain\Category;

final class TestVisAudVis implements CategoryVisitor
{
    private $depth = 0;
}