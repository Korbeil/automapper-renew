<?php

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
