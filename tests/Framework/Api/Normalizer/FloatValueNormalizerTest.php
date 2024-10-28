<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\FloatValueNormalizer;
use PlanB\Type\FloatValue;
use Prophecy\PhpUnit\ProphecyTrait;

final class FloatValueNormalizerTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_only_normalizes_objects_that_implements_the_right_interface()
    {
        $good = $this->prophesize(FloatExample::class)->reveal();
        $bad = $this->prophesize()->reveal();

        $normalizer = new FloatValueNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($good));
        $this->assertFalse($normalizer->supportsNormalization($bad));
    }

    public function test_it_only_denormalizes_values_of_the_right_type()
    {
        $good = 12.5;
        $bad = ['its not a float'];

        $normalizer = new FloatValueNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization($good, FloatExample::class));
        $this->assertFalse($normalizer->supportsDenormalization($bad, FloatExample::class));
    }

    public function test_it_normalizes_and_denormalizes_properly()
    {
        $input = 23;
        $valueObject = new FloatExample($input);

        $normalizer = new FloatValueNormalizer();
        $this->assertEquals($input, $normalizer->normalize($valueObject));
        $this->assertEquals($valueObject, $normalizer->denormalize($input, FloatExample::class));
    }

    public function test_it_supports_types_works_properly()
    {
        $normalizer = new FloatValueNormalizer();
        $this->assertEquals([
            '*' => false,
            FloatValue::class => true
        ], $normalizer->getSupportedTypes('format'));
    }

}

class FloatExample implements FloatValue
{

    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }
}