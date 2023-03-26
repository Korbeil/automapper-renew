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

use Jane\Component\AutoMapper\Generator\Generator;
use Jane\Component\AutoMapper\MapperGeneratorMetadataInterface;
use PhpParser\PrettyPrinter\Standard;

/**
 * Use file system to load mapper, and persist them using a registry.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class FileLoader implements ClassLoaderInterface
{
    private Standard $printer;
    private ?array $registry = null;

    public function __construct(
        private readonly Generator $generator,
        private readonly string $directory,
        private readonly bool $hotReload = true
    ) {
        $this->printer = new Standard();
    }

    public function loadClass(MapperGeneratorMetadataInterface $mapperMetadata): void
    {
        $className = $mapperMetadata->getMapperClassName();
        $classPath = $this->directory.\DIRECTORY_SEPARATOR.$className.'.php';

        if (!$this->hotReload && file_exists($classPath)) {
            require $classPath;

            return;
        }

        $shouldSaveMapper = true;
        if ($this->hotReload) {
            $registry = $this->getRegistry();
            $hash = $mapperMetadata->getHash();
            $shouldSaveMapper = !isset($registry[$className]) || $registry[$className] !== $hash || !file_exists($classPath);
        }

        if ($shouldSaveMapper) {
            $this->saveMapper($mapperMetadata);
        }

        require $classPath;
    }

    public function saveMapper(MapperGeneratorMetadataInterface $mapperGeneratorMetadata): void
    {
        $className = $mapperGeneratorMetadata->getMapperClassName();
        $classPath = $this->directory.\DIRECTORY_SEPARATOR.$className.'.php';
        $classCode = $this->printer->prettyPrint([$this->generator->generate($mapperGeneratorMetadata)]);

        $this->write($classPath, "<?php\n\n".$classCode."\n");
        if ($this->hotReload) {
            $this->addHashToRegistry($className, $mapperGeneratorMetadata->getHash());
        }
    }

    private function addHashToRegistry($className, $hash): void
    {
        $registryPath = $this->directory.\DIRECTORY_SEPARATOR.'registry.php';
        $this->registry[$className] = $hash;
        $this->write($registryPath, "<?php\n\nreturn ".var_export($this->registry, true).";\n");
    }

    private function getRegistry(): array
    {
        if (!$this->registry) {
            $registryPath = $this->directory.\DIRECTORY_SEPARATOR.'registry.php';

            if (!file_exists($registryPath)) {
                $this->registry = [];
            } else {
                $this->registry = require $registryPath;
            }
        }

        return $this->registry;
    }

    private function write(string $file, string $contents): void
    {
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }

        $fp = fopen($file, 'w');

        if (flock($fp, \LOCK_EX)) {
            fwrite($fp, $contents);
        }

        fclose($fp);
    }
}
