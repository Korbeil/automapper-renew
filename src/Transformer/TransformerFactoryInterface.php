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
 * Create transformer.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface TransformerFactoryInterface
{
    /**
     * Get transformer to use when mapping from an array of type to another array of type.
     *
     * @param Type[] $sourceTypes
     * @param Type[] $targetTypes
     */
    public function getTransformer(?array $sourceTypes, ?array $targetTypes, MapperMetadataInterface $mapperMetadata): ?TransformerInterface;
}
