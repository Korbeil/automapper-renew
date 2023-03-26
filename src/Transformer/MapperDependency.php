<?php

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
