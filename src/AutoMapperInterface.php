<?php

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
     * @param null|array|object        $source  Any data object, which may be an object or an array
     * @param string|array|object $target  To which type of data, or data, the source should be mapped
     * @param array               $context Mapper context
     *
     * @return null|array|object The mapped object
     */
    public function map(null|array|object $source, string|array|object $target, array $context = []): null|array|object;
}
