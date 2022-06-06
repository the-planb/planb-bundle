<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\IntegerValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IntegerValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type((int)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, IntegerValue::class) and is_int($data);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $object->toInt();
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof IntegerValue;
    }
}
