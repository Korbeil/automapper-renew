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

use Jane\Component\AutoMapper\MapperMetadataInterface;

/**
 * Reduce array of type to only one type on source and target.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class UniqueTypeTransformerFactory implements TransformerFactoryInterface, PrioritizedTransformerFactoryInterface
{
    public function __construct(
        private readonly ChainTransformerFactory $chainTransformerFactory,
    ) {
    }

    public function getTransformer(?array $sourceTypes, ?array $targetTypes, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        $nbSourceTypes = $sourceTypes ? \count($sourceTypes) : 0;
        $nbTargetTypes = $targetTypes ? \count($targetTypes) : 0;

        if (null === $sourceTypes || 0 === $nbSourceTypes || $nbSourceTypes > 1) {
            return null;
        }

        if (null === $targetTypes || $nbTargetTypes <= 1) {
            return null;
        }

        foreach ($targetTypes as $targetType) {
            if (null === $targetType) {
                continue;
            }

            $transformer = $this->chainTransformerFactory->getTransformer($sourceTypes, [$targetType], $mapperMetadata);

            if (null !== $transformer) {
                return $transformer;
            }
        }

        return null;
    }

    public function getPriority(): int
    {
        return 32;
    }
}
