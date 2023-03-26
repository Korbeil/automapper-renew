<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jane\Component\AutoMapper\Transformer;

/**
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface PrioritizedTransformerFactoryInterface
{
    /**
     * TransformerFactory priority.
     */
    public function getPriority(): int;
}
