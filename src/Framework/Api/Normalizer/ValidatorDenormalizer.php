<?php

namespace PlanB\Framework\Api\Normalizer;

use ApiPlatform\Validator\Exception\ValidationException;
use PlanB\DS\Map\Map;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const FORMAT = '.no.cycle';

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $operation = isset($context['operation']) ?
            $context['operation']->getInput() : [];

        $type = $operation['class'] ?? $type;
        $classMetaData = $this->validator->getMetadataFor($type);

        if (!isset($classMetaData->members)) {
            return $this->denormalizer->denormalize($data, $type, self::FORMAT, $context);
        }

        $constraints = [];
        foreach ($classMetaData->members as $name => $member) {
            $constraints[$name] = Map::collect($member)
                ->flatMap(fn(PropertyMetadata $metadata) => $metadata->constraints)
                ->toArray();
        }

        if (empty($constraints)) {
            return $this->denormalizer->denormalize($data, $type, self::FORMAT, $context);
        }

        $violations = $this->validator->validate($data, new Collection($constraints));
        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }

        return $this->denormalizer->denormalize($data, $type, self::FORMAT, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $format !== self::FORMAT && $this->validator->hasMetadataFor($type);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
//            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
        ];
    }
}
