<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CarbonNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        if ($type === CarbonImmutable::class) {
            return CarbonImmutable::make($data);
        }

        return Carbon::make($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === CarbonImmutable::class || $type === Carbon::class;
    }
}
