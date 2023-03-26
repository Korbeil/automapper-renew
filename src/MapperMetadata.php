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

use Jane\Component\AutoMapper\Extractor\MappingExtractorInterface;
use Jane\Component\AutoMapper\Extractor\PropertyMapping;
use Jane\Component\AutoMapper\Extractor\ReadAccessor;
use Jane\Component\AutoMapper\Extractor\ReadAccessorType;
use Jane\Component\AutoMapper\Transformer\CallbackTransformer;
use Jane\Component\AutoMapper\Transformer\DependentTransformerInterface;

/**
 * Mapper metadata.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
class MapperMetadata implements MapperGeneratorMetadataInterface
{
    /** @var array<string, callable> */
    private array $customMapping = [];

    /** @var array<PropertyMapping>|null */
    private ?array $propertiesMapping = null;

    private ?string $className = null;

    public bool $isConstructorAllowed = true;

    private string $dateTimeFormat = \DateTimeInterface::RFC3339;

    private bool $attributeChecking = true;

    private ?\ReflectionClass $targetReflectionClass = null;

    public function __construct(
        private readonly MapperGeneratorMetadataRegistryInterface $metadataRegistry,
        private readonly MappingExtractorInterface $mappingExtractor,
        private readonly string $source,
        private readonly string $target,
        private readonly string $classPrefix = 'Mapper_',
    ) {
    }

    private function getCachedTargetReflectionClass(): \ReflectionClass
    {
        if (null === $this->targetReflectionClass) {
            $this->targetReflectionClass = new \ReflectionClass($this->getTarget());
        }

        return $this->targetReflectionClass;
    }

    public function getPropertiesMapping(): array
    {
        if (null === $this->propertiesMapping) {
            $this->buildPropertyMapping();
        }

        return $this->propertiesMapping;
    }

    public function getPropertyMapping(string $property): ?PropertyMapping
    {
        return $this->getPropertiesMapping()[$property] ?? null;
    }

    public function hasConstructor(): bool
    {
        if (!$this->isConstructorAllowed()) {
            return false;
        }

        if (\in_array($this->target, ['array', \stdClass::class], true)) {
            return false;
        }

        $reflection = $this->getCachedTargetReflectionClass();
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            return false;
        }

        $parameters = $constructor->getParameters();
        $mandatoryParameters = [];

        foreach ($parameters as $parameter) {
            if (!$parameter->isOptional() && !$parameter->allowsNull()) {
                $mandatoryParameters[] = $parameter;
            }
        }

        if (!$mandatoryParameters) {
            return true;
        }

        foreach ($mandatoryParameters as $mandatoryParameter) {
            $readAccessor = $this->mappingExtractor->getReadAccessor($this->source, $this->target, $mandatoryParameter->getName());

            if (null === $readAccessor) {
                return false;
            }
        }

        return true;
    }

    public function isTargetCloneable(): bool
    {
        try {
            $reflection = $this->getCachedTargetReflectionClass();

            return $reflection->isCloneable() && !$reflection->hasMethod('__clone');
        } catch (\ReflectionException $e) {
            // if we have a \ReflectionException, then we can't clone target
            return false;
        }
    }

    public function canHaveCircularReference(): bool
    {
        $checked = [];

        return $this->checkCircularMapperConfiguration($this, $checked);
    }

    public function getMapperClassName(): string
    {
        if (null !== $this->className) {
            return $this->className;
        }

        return $this->className = sprintf('%s%s_%s', $this->classPrefix, str_replace('\\', '_', $this->source), str_replace('\\', '_', $this->target));
    }

    public function getHash(): string
    {
        $hash = '';

        if (!\in_array($this->source, ['array', \stdClass::class], true) && class_exists($this->source)) {
            $reflection = new \ReflectionClass($this->source);
            $hash .= filemtime($reflection->getFileName());
        }

        if (!\in_array($this->target, ['array', \stdClass::class], true)) {
            $reflection = $this->getCachedTargetReflectionClass();
            $hash .= filemtime($reflection->getFileName());
        }

        return $hash;
    }

    public function isConstructorAllowed(): bool
    {
        return $this->isConstructorAllowed;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getDateTimeFormat(): string
    {
        return $this->dateTimeFormat;
    }

    public function getCallbacks(): array
    {
        return $this->customMapping;
    }

    public function shouldCheckAttributes(): bool
    {
        return $this->attributeChecking;
    }

    /**
     * Set DateTime format to use when generating a mapper.
     */
    public function setDateTimeFormat(string $dateTimeFormat): void
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * Whether or not the constructor should be used.
     */
    public function setConstructorAllowed(bool $isConstructorAllowed): void
    {
        $this->isConstructorAllowed = $isConstructorAllowed;
    }

    /**
     * Set a callable to use when mapping a specific property.
     */
    public function forMember(string $property, callable $callback): void
    {
        $this->customMapping[$property] = $callback;
    }

    /**
     * Whether or not attribute checking code should be generated.
     */
    public function setAttributeChecking(bool $attributeChecking): void
    {
        $this->attributeChecking = $attributeChecking;
    }

    private function buildPropertyMapping(): void
    {
        $this->propertiesMapping = [];

        foreach ($this->mappingExtractor->getPropertiesMapping($this) as $propertyMapping) {
            $this->propertiesMapping[$propertyMapping->property] = $propertyMapping;
        }

        foreach ($this->customMapping as $property => $callback) {
            $this->propertiesMapping[$property] = new PropertyMapping(
                new ReadAccessor(ReadAccessorType::SOURCE, $property),
                $this->mappingExtractor->getWriteMutator($this->source, $this->target, $property),
                null,
                new CallbackTransformer($property),
                $property,
                false
            );
        }
    }

    private function checkCircularMapperConfiguration(MapperGeneratorMetadataInterface $configuration, &$checked): bool
    {
        foreach ($configuration->getPropertiesMapping() as $propertyMapping) {
            if (!$propertyMapping->transformer instanceof DependentTransformerInterface) {
                continue;
            }

            foreach ($propertyMapping->transformer->getDependencies() as $dependency) {
                if (isset($checked[$dependency->name])) {
                    continue;
                }

                $checked[$dependency->name] = true;

                if ($dependency->source === $this->getSource() && $dependency->target === $this->getTarget()) {
                    return true;
                }

                $subConfiguration = $this->metadataRegistry->getMetadata($dependency->source, $dependency->target);

                if (null !== $subConfiguration && true === $this->checkCircularMapperConfiguration($subConfiguration, $checked)) {
                    return true;
                }
            }
        }

        return false;
    }
}
