<?php

namespace Jane\Component\AutoMapper\Normalizer;

use Jane\Component\AutoMapper\AutoMapperInterface;
use Jane\Component\AutoMapper\MapperContext;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Bridge for symfony/serializer.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
class AutoMapperNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private readonly AutoMapperInterface $autoMapper,
    ) {
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $this->autoMapper->map($object, 'array', $this->createAutoMapperContext($context));
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return $this->autoMapper->map($data, $type, $this->createAutoMapperContext($context));
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        if (!\is_object($data) || $data instanceof \stdClass) {
            return false;
        }

        return $this->autoMapper->hasMapper(\get_class($data), 'array');
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $this->autoMapper->hasMapper('array', $type);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    private function createAutoMapperContext(array $serializerContext = []): array
    {
        $context = [
            MapperContext::GROUPS => $serializerContext[AbstractNormalizer::GROUPS] ?? null,
            MapperContext::ALLOWED_ATTRIBUTES => $serializerContext[AbstractNormalizer::ATTRIBUTES] ?? null,
            MapperContext::IGNORED_ATTRIBUTES => $serializerContext[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? null,
            MapperContext::TARGET_TO_POPULATE => $serializerContext[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null,
            MapperContext::CIRCULAR_REFERENCE_LIMIT => $serializerContext[AbstractNormalizer::CIRCULAR_REFERENCE_LIMIT] ?? 1,
            MapperContext::CIRCULAR_REFERENCE_HANDLER => $serializerContext[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER] ?? null,
        ];

        if (\array_key_exists(AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS, $serializerContext) && is_iterable($serializerContext[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS])) {
            foreach ($serializerContext[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS] as $class => $keyArgs) {
                foreach ($keyArgs as $key => $value) {
                    $context[MapperContext::CONSTRUCTOR_ARGUMENTS][$class][$key] = $value;
                }
            }
        }

        return $context;
    }
}
