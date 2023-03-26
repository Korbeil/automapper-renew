<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jane\Component\AutoMapper\Transformer;

use Jane\Component\AutoMapper\Extractor\PropertyMapping;
use Jane\Component\AutoMapper\Generator\UniqueVariableScope;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;

/**
 * Transform a \DateTimeInterface object to a string.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class DateTimeToStringTransformer implements TransformerInterface
{
    public function __construct(
        private readonly string $format = \DateTimeInterface::RFC3339
    ) {
    }

    public function transform(Expr $input, Expr $target, PropertyMapping $propertyMapping, UniqueVariableScope $uniqueVariableScope): array
    {
        return [new Expr\MethodCall($input, 'format', [
            new Arg(new String_($this->format)),
        ]), []];
    }
}
