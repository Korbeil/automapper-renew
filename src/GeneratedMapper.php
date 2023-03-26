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

/**
 * Class derived for each generated mapper.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
abstract class GeneratedMapper implements MapperInterface
{
    protected array $mappers = [];

    /** @var array<string, callable> */
    protected array $callbacks;

    /** @var array<string, callable> */
    protected array $hydrateCallbacks = [];

    /** @var array<string, callable> */
    protected array $extractCallbacks = [];

    protected object $cachedTarget;

    /** @var callable|null */
    protected $circularReferenceHandler = null;

    protected ?int $circularReferenceLimit = null;

    /**
     * Add a callable for a specific property.
     */
    public function addCallback(string $name, callable $callback): void
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * Inject sub mappers.
     */
    public function injectMappers(AutoMapperRegistryInterface $autoMapperRegistry): void
    {
    }

    public function setCircularReferenceHandler(?callable $circularReferenceHandler): void
    {
        $this->circularReferenceHandler = $circularReferenceHandler;
    }

    public function setCircularReferenceLimit(?int $circularReferenceLimit): void
    {
        $this->circularReferenceLimit = $circularReferenceLimit;
    }
}
