<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Domain\Model\Entity;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EntityNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return $this->denormalizer->denormalize($data['@id'], $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {

        return is_array($data) and isset($data['@id']);
    }

    public function getSupportedTypes(): array
    {
        return [
            Entity::class => true, // Supports AuthorListInput and result is cacheable
        ];
    }
}
