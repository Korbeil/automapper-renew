<?php

namespace Jane\Component\AutoMapper\Loader;

use Jane\Component\AutoMapper\Generator\Generator;
use Jane\Component\AutoMapper\MapperGeneratorMetadataInterface;
use PhpParser\PrettyPrinter\Standard;

/**
 * Use file system to load mapper, and persist them using a registry.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 */
final class FileLoader implements ClassLoaderInterface
{
    private $generator;
    private $directory;
    private $hotReload;
    private $printer;
    private $registry;

    public function __construct(Generator $generator, string $directory, bool $hotReload = true)
    {
        $this->generator = $generator;
        $this->directory = $directory;
        $this->hotReload = $hotReload;
        $this->printer = new Standard();
    }

    /**
     * {@inheritdoc}
     */
    public function loadClass(MapperGeneratorMetadataInterface $mapperGeneratorMetadata): void
    {
        $className = $mapperGeneratorMetadata->getMapperClassName();
        $classPath = $this->directory . \DIRECTORY_SEPARATOR . $className . '.php';

        if (!$this->hotReload && file_exists($classPath)) {
            require $classPath;

            return;
        }

        $shouldSaveMapper = true;
        if ($this->hotReload) {
            $registry = $this->getRegistry();
            $hash = $mapperGeneratorMetadata->getHash();
            $shouldSaveMapper = !isset($registry[$className]) || $registry[$className] !== $hash || !file_exists($classPath);
        }

        if ($shouldSaveMapper) {
            $this->saveMapper($mapperGeneratorMetadata);
        }

        require $classPath;
    }

    public function saveMapper(MapperGeneratorMetadataInterface $mapperGeneratorMetadata): void
    {
        $className = $mapperGeneratorMetadata->getMapperClassName();
        $classPath = $this->directory . \DIRECTORY_SEPARATOR . $className . '.php';
        $classCode = $this->printer->prettyPrint([$this->generator->generate($mapperGeneratorMetadata)]);

        $this->write($classPath, "<?php\n\n" . $classCode . "\n");
        if ($this->hotReload) {
            $this->addHashToRegistry($className, $mapperGeneratorMetadata->getHash());
        }
    }

    private function addHashToRegistry($className, $hash): void
    {
        $registryPath = $this->directory . \DIRECTORY_SEPARATOR . 'registry.php';
        $this->registry[$className] = $hash;
        $this->write($registryPath, "<?php\n\nreturn " . var_export($this->registry, true) . ";\n");
    }

    private function getRegistry()
    {
        if (!$this->registry) {
            $registryPath = $this->directory . \DIRECTORY_SEPARATOR . 'registry.php';

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

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $contents);
        }

        fclose($fp);
    }
}
