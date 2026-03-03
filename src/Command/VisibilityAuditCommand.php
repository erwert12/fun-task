<?php
namespace FunTask\Command;

use FunTask\Domain\CategoryTreeLoader;
use FunTask\Visitor\VisibilityAuditVisitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

