<?php

namespace Jane\Component\AutoMapper\Transformer;

use Jane\Component\AutoMapper\AutoMapperRegistryInterface;
use Jane\Component\AutoMapper\MapperMetadataInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class ObjectTransformerFactory extends AbstractUniqueTypeTransformerFactory implements PrioritizedTransformerFactoryInterface
{
    public function __construct(
        private readonly AutoMapperRegistryInterface $autoMapperRegistry,
    ) {
    }

    protected function createTransformer(Type $sourceType, Type $targetType, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        // Only deal with source type being an object or an array that is not a collection
        if (!$this->isObjectType($sourceType) || !$this->isObjectType($targetType)) {
            return null;
        }

        $sourceTypeName = 'array';
        $targetTypeName = 'array';

        if (Type::BUILTIN_TYPE_OBJECT === $sourceType->getBuiltinType()) {
            $sourceTypeName = $sourceType->getClassName();
        }

        if (Type::BUILTIN_TYPE_OBJECT === $targetType->getBuiltinType()) {
            $targetTypeName = $targetType->getClassName();
        }

        if (null !== $sourceTypeName && null !== $targetTypeName && $this->autoMapperRegistry->hasMapper($sourceTypeName, $targetTypeName)) {
            return new ObjectTransformer($sourceType, $targetType);
        }

        return null;
    }

    private function isObjectType(Type $type): bool
    {
        if (!\in_array($type->getBuiltinType(), [Type::BUILTIN_TYPE_OBJECT, Type::BUILTIN_TYPE_ARRAY])) {
            return false;
        }

        if (Type::BUILTIN_TYPE_ARRAY === $type->getBuiltinType() && $type->isCollection()) {
            return false;
        }

        if (is_subclass_of($type->getClassName(), \UnitEnum::class)) {
            return false;
        }

        return true;
    }

    public function getPriority(): int
    {
        return 2;
    }
}
