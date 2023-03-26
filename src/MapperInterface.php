<?php

namespace Jane\Component\AutoMapper;

/**
 * Interface implemented by a single mapper.
 *
 * Each specific mapper should implement this interface
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface MapperInterface
{
    /**
     * @param mixed $value   Value to map
     * @param array $context Options mapper have access to
     *
     * @return mixed The mapped value
     */
    public function &map(mixed $value, array $context = []): mixed;
}
