<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\ArrayValue;
use PlanB\Type\StringValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class StringValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type((string)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, StringValue::class) and is_string($data);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return (string)$object;
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof StringValue;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
//            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            StringValue::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
}
