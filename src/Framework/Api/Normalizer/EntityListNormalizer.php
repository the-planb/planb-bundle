<?php

namespace PlanB\Framework\Api\Normalizer;

use PlanB\Domain\Model\EntityListInput;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class EntityListNormalizer implements DenormalizerInterface, SerializerAwareInterface
{
    private DenormalizerInterface $serializer;

    public function setSerializer(SerializerInterface $serializer)
    {
        if (!$serializer instanceof DenormalizerInterface) {
            throw new LogicException(sprintf('The decorated normalizer must be an instance of "%s".', DenormalizerInterface::class));
        }

        $this->serializer = $serializer;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): EntityListInput
    {
        $values = [];
        foreach ($data as $key => $item) {
            $values[$key] = $this->denormalizeItem($item, $format, $context);
        }

        return $this->collect($values);
    }

    private function denormalizeItem(string|array $item, string $format = null, array $context = []): object|array
    {
        if (is_string($item)) {
            return $this->itemFromIri($item, $format, $context);
        }

        if (isset($item['@id']) && is_string($item['@id'])) {
            return $this->itemFromIri($item['@id'], $format, $context);
        }

        return array_filter((array)$this->itemFromArray($item, $format, $context));
    }

    protected function convert(mixed $input, string $type, mixed $format, array $context): object
    {
        return $this->serializer->denormalize($input, $type, $format, $context);
    }

    abstract protected function collect(array $data): EntityListInput;

    abstract protected function itemFromIri(string $input, mixed $format, array $context): object;

    abstract protected function itemFromArray(array $input, mixed $format, array $context): object;
}
