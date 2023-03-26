<?php

namespace Jane\Component\AutoMapper\Extractor;

use Jane\Component\AutoMapper\Exception\CompileException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

/**
 * @internal
 *
 * Read accessor types.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
enum ReadAccessorType
{
    case METHOD;
    case PROPERTY;
    case ARRAY_DIMENSION;
    case SOURCE;
}