<?php

namespace Jane\Component\AutoMapper\Tests\Transformer;

use Jane\Component\AutoMapper\MapperMetadata;
use Jane\Component\AutoMapper\Tests\Fixtures\AddressType;
use Jane\Component\AutoMapper\Tests\Fixtures\UnitAddressType;
use Jane\Component\AutoMapper\Transformer\CopyTransformer;
use Jane\Component\AutoMapper\Transformer\EnumTransformerFactory;
use Jane\Component\AutoMapper\Transformer\SourceEnumTransformer;
use Jane\Component\AutoMapper\Transformer\SymfonyUidCopyTransformer;
use Jane\Component\AutoMapper\Transformer\SymfonyUidTransformerFactory;
use Jane\Component\AutoMapper\Transformer\TargetEnumTransformer;
use Jane\Component\AutoMapper\Transformer\TransformerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Uid\Ulid;

/**
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
class EnumTransformerFactoryTest extends TestCase
{
    public function testNoTransformer(): void
    {
        $transformer = $this->makeTransformer(
            new Type('object'),
            new Type('object'),
        );

        self::assertNull($transformer);
    }

    public function testSourceIsEnum(): void
    {
        $transformer = $this->makeTransformer(
            new Type('object', class: UnitAddressType::class),
            new Type('string'),
        );

        self::assertNull($transformer);

        $transformer = $this->makeTransformer(
            new Type('object', class: AddressType::class),
            new Type('string'),
        );

        self::assertNotNull($transformer);
        self::assertInstanceOf(SourceEnumTransformer::class, $transformer);
    }

    public function testTargetIsEnum(): void
    {
        $transformer = $this->makeTransformer(
            new Type('string'),
            new Type('object', class: UnitAddressType::class),
        );

        self::assertNull($transformer);

        $transformer = $this->makeTransformer(
            new Type('string'),
            new Type('object', class: AddressType::class),
        );

        self::assertNotNull($transformer);
        self::assertInstanceOf(TargetEnumTransformer::class, $transformer);

    }

    public function testGetCopyTransformer(): void
    {
        $transformer = $this->makeTransformer(
            new Type('object', false, UnitAddressType::class),
            new Type('object', false, AddressType::class),
        );

        self::assertNotNull($transformer);
        self::assertInstanceOf(CopyTransformer::class, $transformer);
    }

    private function makeTransformer(Type $source, Type $target): ?TransformerInterface
    {
        $factory = new EnumTransformerFactory();
        $mapperMetadata = $this->getMockBuilder(MapperMetadata::class)->disableOriginalConstructor()->getMock();

        return $factory->getTransformer([$source], [$target], $mapperMetadata);
    }
}
