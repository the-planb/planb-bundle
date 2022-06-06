<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\BooleanValueNormalizer;
use PlanB\Type\BooleanValue;
use Prophecy\PhpUnit\ProphecyTrait;

final class BooleanObjectNormalizerTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_only_normalizes_objects_that_implements_the_right_interface()
    {
        $good = $this->prophesize(BooleanExample::class)->reveal();
        $bad = $this->prophesize()->reveal();

        $normalizer = new BooleanValueNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($good));
        $this->assertFalse($normalizer->supportsNormalization($bad));
    }

    public function test_it_only_denormalizes_values_of_the_right_type()
    {
        $good = false;
        $bad = ['its not a boolean'];

        $normalizer = new BooleanValueNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization($good, BooleanExample::class));
        $this->assertFalse($normalizer->supportsDenormalization($bad, BooleanExample::class));
    }

    public function test_it_normalizes_and_denormalizes_properly()
    {
        $input = true;
        $valueObject = new BooleanExample($input);

        $normalizer = new BooleanValueNormalizer();
        $this->assertEquals($input, $normalizer->normalize($valueObject));
        $this->assertEquals($valueObject, $normalizer->denormalize($input, BooleanExample::class));
    }

}

class BooleanExample implements BooleanValue
{

    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function toBoolean(): bool
    {
        return $this->value;
    }
}