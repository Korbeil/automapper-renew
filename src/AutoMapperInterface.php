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
 * An auto mapper has the role of mapping a source to a target.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface AutoMapperInterface
{
    /**
     * Maps data from a source to a target.
     *
     * @param array|object|null   $source  Any data object, which may be an object or an array
     * @param string|array|object $target  To which type of data, or data, the source should be mapped
     * @param array               $context Mapper context
     *
     * @return array|object|null The mapped object
     */
    public function map(null|array|object $source, string|array|object $target, array $context = []): null|array|object;
}
