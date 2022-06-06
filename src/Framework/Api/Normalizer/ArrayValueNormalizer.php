<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\ArrayValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArrayValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type((array)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, ArrayValue::class) and is_array($data);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $object->toArray();
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof ArrayValue;
    }
}
