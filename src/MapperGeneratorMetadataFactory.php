<?php

namespace Jane\Component\AutoMapper;

use Jane\Component\AutoMapper\Extractor\FromSourceMappingExtractor;
use Jane\Component\AutoMapper\Extractor\FromTargetMappingExtractor;
use Jane\Component\AutoMapper\Extractor\SourceTargetMappingExtractor;

/**
 * Metadata factory, used to auto-registering new mapping without creating them.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class MapperGeneratorMetadataFactory implements MapperGeneratorMetadataFactoryInterface
{
    public function __construct(
        private readonly SourceTargetMappingExtractor $sourceTargetPropertiesMappingExtractor,
        private readonly FromSourceMappingExtractor $fromSourcePropertiesMappingExtractor,
        private readonly FromTargetMappingExtractor $fromTargetPropertiesMappingExtractor,
        private readonly string $classPrefix = 'Mapper_',
        private readonly bool $attributeChecking = true,
        private readonly string $dateTimeFormat = \DateTimeInterface::RFC3339
    ) {
    }

    /**
     * Create metadata for a source and target.
     */
    public function create(MapperGeneratorMetadataRegistryInterface $autoMapperRegister, string $source, string $target): MapperGeneratorMetadataInterface
    {
        $extractor = $this->sourceTargetPropertiesMappingExtractor;

        if ('array' === $source || 'stdClass' === $source) {
            $extractor = $this->fromTargetPropertiesMappingExtractor;
        }

        if ('array' === $target || 'stdClass' === $target) {
            $extractor = $this->fromSourcePropertiesMappingExtractor;
        }

        $mapperMetadata = new MapperMetadata($autoMapperRegister, $extractor, $source, $target, $this->classPrefix);
        $mapperMetadata->setAttributeChecking($this->attributeChecking);
        $mapperMetadata->setDateTimeFormat($this->dateTimeFormat);

        return $mapperMetadata;
    }
}
