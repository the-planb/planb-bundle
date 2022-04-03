<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\CarbonNormalizer;
use Symfony\Component\Serializer\Serializer;

final class CarbonNormalizerTest extends TestCase
{

    public function test_it_can_denormalize_an_instance_of_carbon()
    {
        $serializer = new Serializer([
            new CarbonNormalizer(),
        ]);

        $date = $serializer->denormalize('2012-10-25 17:30:15', Carbon::class);

        $this->assertInstanceOf(Carbon::class, $date);
        $this->assertSame('2012-10-25T17:30:15.000000Z', $date->toISOString());
    }

    public function test_it_can_denormalize_an_instance_of_carbon_immutable()
    {
        $serializer = new Serializer([
            new CarbonNormalizer(),
        ]);

        $date = $serializer->denormalize('2012-10-25 17:30:15', CarbonImmutable::class);

        $this->assertInstanceOf(CarbonImmutable::class, $date);
        $this->assertSame('2012-10-25T17:30:15.000000Z', $date->toISOString());
    }
}
