<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jane\Component\AutoMapper\Extractor;

/**
 * @internal
 *
 * Write mutator types
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
enum WriteMutatorType
{
    case METHOD;
    case PROPERTY;
    case ARRAY_DIMENSION;
    case CONSTRUCTOR;
    case ADDER_AND_REMOVER;
}
