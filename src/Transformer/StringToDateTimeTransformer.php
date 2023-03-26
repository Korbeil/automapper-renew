<?php

namespace Jane\Component\AutoMapper\Transformer;

use Jane\Component\AutoMapper\Extractor\PropertyMapping;
use Jane\Component\AutoMapper\Generator\UniqueVariableScope;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;

/**
 * Transform a string to a \DateTimeInterface object.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class StringToDateTimeTransformer implements TransformerInterface
{

    public function __construct(
        private readonly string $className,
        private readonly string $format = \DateTimeInterface::RFC3339,
    ) {
    }

    public function transform(Expr $input, Expr $target, PropertyMapping $propertyMapping, UniqueVariableScope $uniqueVariableScope): array
    {
        $className = \DateTimeInterface::class === $this->className ? \DateTimeImmutable::class : $this->className;

        return [new Expr\StaticCall(new Name\FullyQualified($className), 'createFromFormat', [
            new Arg(new String_($this->format)),
            new Arg($input),
        ]), []];
    }
}
