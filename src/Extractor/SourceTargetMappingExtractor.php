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

use Jane\Component\AutoMapper\MapperMetadataInterface;

/**
 * Extracts mapping between two objects, only gives properties that have the same name.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
class SourceTargetMappingExtractor extends MappingExtractor
{
    public function getPropertiesMapping(MapperMetadataInterface $mapperMetadata): array
    {
        $sourceProperties = $this->propertyInfoExtractor->getProperties($mapperMetadata->getSource());
        $targetProperties = $this->propertyInfoExtractor->getProperties($mapperMetadata->getTarget());

        if (null === $sourceProperties || null === $targetProperties) {
            return [];
        }

        $sourceProperties = array_unique($sourceProperties);
        $targetProperties = array_unique($targetProperties);

        $mapping = [];

        foreach ($sourceProperties as $property) {
            if (!$this->propertyInfoExtractor->isReadable($mapperMetadata->getSource(), $property)) {
                continue;
            }

            if (\in_array($property, $targetProperties, true)) {
                $targetMutatorConstruct = $this->getWriteMutator($mapperMetadata->getSource(), $mapperMetadata->getTarget(), $property, [
                    'enable_constructor_extraction' => true,
                ]);

                if ((null === $targetMutatorConstruct || null === $targetMutatorConstruct->getParameter()) && !$this->propertyInfoExtractor->isWritable($mapperMetadata->getTarget(), $property)) {
                    continue;
                }

                $sourceTypes = $this->propertyInfoExtractor->getTypes($mapperMetadata->getSource(), $property);
                $targetTypes = $this->propertyInfoExtractor->getTypes($mapperMetadata->getTarget(), $property);
                $transformer = $this->transformerFactory->getTransformer($sourceTypes, $targetTypes, $mapperMetadata);

                if (null === $transformer) {
                    continue;
                }

                $sourceAccessor = $this->getReadAccessor($mapperMetadata->getSource(), $mapperMetadata->getTarget(), $property);
                $targetMutator = $this->getWriteMutator($mapperMetadata->getSource(), $mapperMetadata->getTarget(), $property, [
                    'enable_constructor_extraction' => false,
                ]);

                $maxDepthSource = $this->getMaxDepth($mapperMetadata->getSource(), $property);
                $maxDepthTarget = $this->getMaxDepth($mapperMetadata->getTarget(), $property);
                $maxDepth = null;

                if (null !== $maxDepthSource && null !== $maxDepthTarget) {
                    $maxDepth = min($maxDepthSource, $maxDepthTarget);
                } elseif (null !== $maxDepthSource) {
                    $maxDepth = $maxDepthSource;
                } elseif (null !== $maxDepthTarget) {
                    $maxDepth = $maxDepthTarget;
                }

                $mapping[] = new PropertyMapping(
                    $sourceAccessor,
                    $targetMutator,
                    WriteMutatorType::CONSTRUCTOR === $targetMutatorConstruct->getType() ? $targetMutatorConstruct : null,
                    $transformer,
                    $property,
                    false,
                    $this->getGroups($mapperMetadata->getSource(), $property),
                    $this->getGroups($mapperMetadata->getTarget(), $property),
                    $maxDepth
                );
            }
        }

        return $mapping;
    }
}
