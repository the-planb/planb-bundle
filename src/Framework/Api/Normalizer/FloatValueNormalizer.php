<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\FloatValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FloatValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type((float)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, FloatValue::class) and (is_float($data) or (is_int($data)));
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $object->toFloat();
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof FloatValue;
    }
}
