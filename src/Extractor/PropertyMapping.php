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

use Jane\Component\AutoMapper\Transformer\TransformerInterface;

/**
 * Property mapping.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class PropertyMapping
{
    public function __construct(
        public readonly ReadAccessor $readAccessor,
        public readonly ?WriteMutator $writeMutator,
        public readonly ?WriteMutator $writeMutatorConstructor,
        public readonly TransformerInterface $transformer,
        public readonly string $property,
        public readonly bool $checkExists = false,
        public readonly ?array $sourceGroups = null,
        public readonly ?array $targetGroups = null,
        public readonly ?int $maxDepth = null
    ) {
    }
}
