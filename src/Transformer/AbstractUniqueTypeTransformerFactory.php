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
 * Abstract transformer which is used by transformer needing transforming only from one single type to one single type.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
abstract class AbstractUniqueTypeTransformerFactory implements TransformerFactoryInterface
{
    public function getTransformer(?array $sourceTypes, ?array $targetTypes, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        $nbSourceTypes = $sourceTypes ? \count($sourceTypes) : 0;
        $nbTargetTypes = $targetTypes ? \count($targetTypes) : 0;

        if (0 === $nbSourceTypes || $nbSourceTypes > 1 || !$sourceTypes[0] instanceof Type) {
            return null;
        }

        if (0 === $nbTargetTypes || $nbTargetTypes > 1 || !$targetTypes[0] instanceof Type) {
            return null;
        }

        return $this->createTransformer($sourceTypes[0], $targetTypes[0], $mapperMetadata);
    }

    abstract protected function createTransformer(Type $sourceType, Type $targetType, MapperMetadataInterface $mapperMetadata): ?TransformerInterface;
}
