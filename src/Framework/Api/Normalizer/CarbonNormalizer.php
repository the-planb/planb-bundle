<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CarbonNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if ($type === CarbonImmutable::class) {
            return CarbonImmutable::make($data);
        }

        return Carbon::make($data);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CarbonImmutable::class || $type === Carbon::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
//            'object' => null,             // Doesn't support any classes or interfaces
            '*' => false,                 // Supports any other types, but the result is not cacheable
            CarbonImmutable::class => true, // Supports MyCustomClass and result is cacheable
        ];
    }


}
