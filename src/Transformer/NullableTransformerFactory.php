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
use Symfony\Component\PropertyInfo\Type;

/**
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class NullableTransformerFactory implements TransformerFactoryInterface, PrioritizedTransformerFactoryInterface
{
    public function __construct(
        private readonly ChainTransformerFactory $chainTransformerFactory
    ) {
    }

    public function getTransformer(?array $sourceTypes, ?array $targetTypes, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        $nbSourceTypes = $sourceTypes ? \count($sourceTypes) : 0;

        if (null === $sourceTypes || 0 === $nbSourceTypes || $nbSourceTypes > 1) {
            return null;
        }

        $propertyType = $sourceTypes[0];

        if (!$propertyType->isNullable()) {
            return null;
        }

        $isTargetNullable = false;

        foreach ($targetTypes as $targetType) {
            if ($targetType->isNullable()) {
                $isTargetNullable = true;

                break;
            }
        }

        $subTransformer = $this->chainTransformerFactory->getTransformer([new Type(
            $propertyType->getBuiltinType(),
            false,
            $propertyType->getClassName(),
            $propertyType->isCollection(),
            $propertyType->getCollectionKeyTypes(),
            $propertyType->getCollectionValueTypes()
        )], $targetTypes, $mapperMetadata);

        if (null === $subTransformer) {
            return null;
        }

        // Remove nullable property here to avoid infinite loop
        return new NullableTransformer($subTransformer, $isTargetNullable);
    }

    public function getPriority(): int
    {
        return 64;
    }
}
