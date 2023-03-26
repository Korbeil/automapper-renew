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

use Jane\Component\AutoMapper\Exception\CompileException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

/**
 * Read accessor tell how to read from a property.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class ReadAccessor
{
    public function __construct(
        private readonly ReadAccessorType $type,
        private readonly string $name,
        private readonly bool $private = false
    ) {
    }

    /**
     * Get AST expression for reading property from an input.
     *
     * @throws CompileException
     */
    public function getExpression(Expr\Variable $input): Expr
    {
        if (ReadAccessorType::METHOD === $this->type) {
            return new Expr\MethodCall($input, $this->name);
        }

        if (ReadAccessorType::PROPERTY === $this->type) {
            if ($this->private) {
                return new Expr\FuncCall(
                    new Expr\ArrayDimFetch(new Expr\PropertyFetch(new Expr\Variable('this'), 'extractCallbacks'), new Scalar\String_($this->name)),
                    [
                        new Arg($input),
                    ]
                );
            }

            return new Expr\PropertyFetch($input, $this->name);
        }

        if (ReadAccessorType::ARRAY_DIMENSION === $this->type) {
            return new Expr\ArrayDimFetch($input, new Scalar\String_($this->name));
        }

        if (ReadAccessorType::SOURCE === $this->type) {
            return $input;
        }

        throw new CompileException('Invalid accessor for read expression');
    }

    /**
     * Get AST expression for binding closure when dealing with a private property.
     */
    public function getExtractCallback($className): ?Expr
    {
        if (ReadAccessorType::PROPERTY !== $this->type || !$this->private) {
            return null;
        }

        return new Expr\StaticCall(new Name\FullyQualified(\Closure::class), 'bind', [
            new Arg(new Expr\Closure([
                'params' => [
                    new Param(new Expr\Variable('object')),
                ],
                'stmts' => [
                    new Stmt\Return_(new Expr\PropertyFetch(new Expr\Variable('object'), $this->name)),
                ],
            ])),
            new Arg(new Expr\ConstFetch(new Name('null'))),
            new Arg(new Scalar\String_(new Name\FullyQualified($className))),
        ]);
    }
}
