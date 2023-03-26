<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jane\Component\AutoMapper\Loader;

use Jane\Component\AutoMapper\MapperGeneratorMetadataInterface;

/**
 * Loads (require) a mapping given metadata.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
interface ClassLoaderInterface
{
    public function loadClass(MapperGeneratorMetadataInterface $mapperMetadata): void;
}
