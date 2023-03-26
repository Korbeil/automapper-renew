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
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;

/**
 * Transform DateTimeImmutable to DateTime.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class DateTimeImmutableToMutableTransformer implements TransformerInterface
{
    public function transform(Expr $input, Expr $target, PropertyMapping $propertyMapping, UniqueVariableScope $uniqueVariableScope): array
    {
        return [
            new Expr\StaticCall(new Name\FullyQualified(\DateTime::class), 'createFromFormat', [
                new Arg(new String_(\DateTimeInterface::RFC3339)),
                new Arg(new Expr\MethodCall($input, 'format', [
                    new Arg(new String_(\DateTimeInterface::RFC3339)),
                ])),
            ]),
            [],
        ];
    }
}
