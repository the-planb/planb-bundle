<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Type\BooleanValue;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BooleanValueNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type((bool)$data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, BooleanValue::class) and is_bool($data);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $object->toBoolean();
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof BooleanValue;
    }
}
