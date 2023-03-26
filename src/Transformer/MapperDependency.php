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

/**
 * Represent a dependency on a mapper (allow to inject sub mappers).
 *
 * @internal
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class MapperDependency
{
    public function __construct(
        public readonly string $name,
        public readonly string $source,
        public readonly string $target,
    ) {
    }
}
