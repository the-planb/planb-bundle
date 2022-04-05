<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Domain\Model\EntityId;
<<<<<<< Updated upstream
=======
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
>>>>>>> Stashed changes
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EntityIdNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return (string)$object;
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof EntityId;
    }
<<<<<<< Updated upstream
=======

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return is_subclass_of($type, EntityId::class);
    }


    public function getSupportedTypes(?string $format): array
    {
        return [
//            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            EntityId::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }
>>>>>>> Stashed changes
}
