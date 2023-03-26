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

/**
 * Metadata factory, used to auto-registering new mapping without creating them.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface MapperGeneratorMetadataFactoryInterface
{
    public function create(MapperGeneratorMetadataRegistryInterface $autoMapperRegister, string $source, string $target): MapperGeneratorMetadataInterface;
}
