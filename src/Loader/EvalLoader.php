<?php

namespace Jane\Component\AutoMapper\Loader;

use Jane\Component\AutoMapper\Generator\Generator;
use Jane\Component\AutoMapper\MapperGeneratorMetadataInterface;
use PhpParser\PrettyPrinter\Standard;

/**
 * Use eval to load mappers.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class EvalLoader implements ClassLoaderInterface
{
    private Standard $printer;

    public function __construct(
        private readonly Generator $generator,
    ) {
        $this->printer = new Standard();
    }

    public function loadClass(MapperGeneratorMetadataInterface $mapperMetadata): void
    {
        $class = $this->generator->generate($mapperMetadata);

        eval($this->printer->prettyPrint([$class]));
    }
}
