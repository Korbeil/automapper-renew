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
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class MultipleTransformerFactory implements TransformerFactoryInterface, PrioritizedTransformerFactoryInterface
{
    public function __construct(
        private readonly ChainTransformerFactory $chainTransformerFactory,
    ) {
    }

    public function getTransformer(?array $sourceTypes, ?array $targetTypes, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        if (null === $sourceTypes || \count($sourceTypes) <= 1) {
            return null;
        }

        $transformers = [];

        foreach ($sourceTypes as $sourceType) {
            $transformer = $this->chainTransformerFactory->getTransformer([$sourceType], $targetTypes, $mapperMetadata);

            if (null !== $transformer) {
                $transformers[] = [
                    'transformer' => $transformer,
                    'type' => $sourceType,
                ];
            }
        }

        if (\count($transformers) > 1) {
            return new MultipleTransformer($transformers);
        }

        if (1 === \count($transformers)) {
            return $transformers[0]['transformer'];
        }

        return null;
    }

    public function getPriority(): int
    {
        return 128;
    }
}
