<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\BooleanValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BooleanValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return new $type((bool)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_subclass_of($type, BooleanValue::class) and is_bool($data);
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        return $object->toBoolean();
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof BooleanValue;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
//            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            BooleanValue::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
}
