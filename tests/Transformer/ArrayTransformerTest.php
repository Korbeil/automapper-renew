<?php

namespace Jane\Component\AutoMapper\Tests\Transformer;

use Jane\Component\AutoMapper\Transformer\ArrayTransformer;
use Jane\Component\AutoMapper\Transformer\BuiltinTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Type;

/**
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
class ArrayTransformerTest extends TestCase
{
    use EvalTransformerTrait;

    public function testArrayToArray(): void
    {
        $transformer = new ArrayTransformer(new BuiltinTransformer(new Type('string'), [new Type('string')]));
        $output = $this->evalTransformer($transformer, ['test']);

        self::assertEquals(['test'], $output);
    }
}
