<?php

namespace Jane\Component\AutoMapper\Extractor;

use Jane\Component\AutoMapper\Exception\CompileException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

/**
 * Writes mutator tell how to write to a property.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class WriteMutator
{
    public function __construct(
        private readonly WriteMutatorType      $type,
        private readonly string                $name,
        private readonly bool                  $private = false,
        private readonly ?\ReflectionParameter $parameter = null
    ) {
    }

    public function getType(): WriteMutatorType
    {
        return $this->type;
    }

    /**
     * Get AST expression for writing from a value to an output.
     *
     * @throws CompileException
     */
    public function getExpression(Expr\Variable $output, Expr $value, bool $byRef = false): ?Expr
    {
        if (WriteMutatorType::METHOD === $this->type || WriteMutatorType::ADDER_AND_REMOVER === $this->type) {
            return new Expr\MethodCall($output, $this->name, [
                new Arg($value),
            ]);
        }

        if (WriteMutatorType::PROPERTY === $this->type) {
            if ($this->private) {
                return new Expr\FuncCall(
                    new Expr\ArrayDimFetch(new Expr\PropertyFetch(new Expr\Variable('this'), 'hydrateCallbacks'), new Scalar\String_($this->name)),
                    [
                        new Arg($output),
                        new Arg($value),
                    ]
                );
            }
            if ($byRef) {
                return new Expr\AssignRef(new Expr\PropertyFetch($output, $this->name), $value);
            }

            return new Expr\Assign(new Expr\PropertyFetch($output, $this->name), $value);
        }

        if (WriteMutatorType::ARRAY_DIMENSION === $this->type) {
            if ($byRef) {
                return new Expr\AssignRef(new Expr\ArrayDimFetch($output, new Scalar\String_($this->name)), $value);
            }

            return new Expr\Assign(new Expr\ArrayDimFetch($output, new Scalar\String_($this->name)), $value);
        }

        throw new CompileException('Invalid accessor for write expression');
    }

    /**
     * Get AST expression for binding closure when dealing with private property.
     */
    public function getHydrateCallback($className): ?Expr
    {
        if (WriteMutatorType::PROPERTY !== $this->type || !$this->private) {
            return null;
        }

        return new Expr\StaticCall(new Name\FullyQualified(\Closure::class), 'bind', [
            new Arg(new Expr\Closure([
                'params' => [
                    new Param(new Expr\Variable('object')),
                    new Param(new Expr\Variable('value')),
                ],
                'stmts' => [
                    new Stmt\Expression(new Expr\Assign(new Expr\PropertyFetch(new Expr\Variable('object'), $this->name), new Expr\Variable('value'))),
                ],
            ])),
            new Arg(new Expr\ConstFetch(new Name('null'))),
            new Arg(new Scalar\String_(new Name\FullyQualified($className))),
        ]);
    }

    /**
     * Get reflection parameter.
     */
    public function getParameter(): ?\ReflectionParameter
    {
        return $this->parameter;
    }
}
