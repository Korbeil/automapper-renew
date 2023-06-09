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
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Ulid;

/**
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class SymfonyUidTransformerFactory extends AbstractUniqueTypeTransformerFactory implements PrioritizedTransformerFactoryInterface
{
    /**
     * @var array<string, array{0: bool, 1: bool}>
     */
    private array $reflectionCache = [];

    protected function createTransformer(Type $sourceType, Type $targetType, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        $isSourceUid = $this->isUid($sourceType);
        $isTargetUid = $this->isUid($targetType);

        if ($isSourceUid && $isTargetUid) {
            return new SymfonyUidCopyTransformer();
        }

        if ($isSourceUid) {
            return new SymfonyUidToStringTransformer($this->reflectionCache[$sourceType->getClassName()][1]);
        }

        if ($isTargetUid) {
            return new StringToSymfonyUidTransformer($targetType->getClassName());
        }

        return null;
    }

    private function isUid(Type $type): bool
    {
        if (Type::BUILTIN_TYPE_OBJECT !== $type->getBuiltinType()) {
            return false;
        }

        if (null === $type->getClassName()) {
            return false;
        }

        if (!\array_key_exists($type->getClassName(), $this->reflectionCache)) {
            $reflClass = new \ReflectionClass($type->getClassName());
            $this->reflectionCache[$type->getClassName()] = [$reflClass->isSubclassOf(AbstractUid::class), Ulid::class === $type->getClassName()];
        }

        return $this->reflectionCache[$type->getClassName()][0];
    }

    public function getPriority(): int
    {
        return 24;
    }
}
