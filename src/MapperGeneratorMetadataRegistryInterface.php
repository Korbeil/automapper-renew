<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jane\Component\AutoMapper;

use Jane\Component\AutoMapper\Transformer\TransformerFactoryInterface;

/**
 * Registry of metadata.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface MapperGeneratorMetadataRegistryInterface
{
    /**
     * Register metadata.
     */
    public function register(MapperGeneratorMetadataInterface $configuration): void;

    /**
     * Bind custom TransformerFactory to the AutoMapper.
     */
    public function bindTransformerFactory(TransformerFactoryInterface $transformerFactory): void;

    /**
     * Get metadata for a source and a target.
     */
    public function getMetadata(string $source, string $target): ?MapperGeneratorMetadataInterface;
}
