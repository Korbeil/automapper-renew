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

/**
 * Transform a scalar into a BackendEnum.
 *
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class TargetEnumTransformer implements TransformerInterface
{
    public function __construct(
        private readonly string $targetClassName,
    ) {
    }

    public function transform(Expr $input, Expr $target, PropertyMapping $propertyMapping, UniqueVariableScope $uniqueVariableScope): array
    {
        return [new Expr\StaticCall(new Name\FullyQualified($this->targetClassName), 'from', [
            new Arg($input),
        ]), []];
    }
}
