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
 * Allows to retrieve a mapper.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface AutoMapperRegistryInterface
{
    /**
     * Gets a specific mapper for a source type and a target type.
     *
     * @param string $source Source type
     * @param string $target Target type
     *
     * @return MapperInterface return associated mapper
     */
    public function getMapper(string $source, string $target): MapperInterface;

    /**
     * Does a specific mapper exist.
     *
     * @param string $source Source type
     * @param string $target Target type
     */
    public function hasMapper(string $source, string $target): bool;
}
