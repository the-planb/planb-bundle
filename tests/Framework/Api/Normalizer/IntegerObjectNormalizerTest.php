<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\IntegerValueNormalizer;
use PlanB\Type\IntegerValue;
use Prophecy\PhpUnit\ProphecyTrait;

final class IntegerObjectNormalizerTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_only_normalizes_objects_that_implements_the_right_interface()
    {
        $good = $this->prophesize(IntegerExample::class)->reveal();
        $bad = $this->prophesize()->reveal();

        $normalizer = new IntegerValueNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($good));
        $this->assertFalse($normalizer->supportsNormalization($bad));
    }

    public function test_it_only_denormalizes_values_of_the_right_type()
    {
        $good = 12;
        $bad = ['its not an integer'];

        $normalizer = new IntegerValueNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization($good, IntegerExample::class));
        $this->assertFalse($normalizer->supportsDenormalization($bad, IntegerExample::class));
    }

    public function test_it_normalizes_and_denormalizes_properly()
    {
        $input = 23;
        $valueObject = new IntegerExample($input);

        $normalizer = new IntegerValueNormalizer();
        $this->assertEquals($input, $normalizer->normalize($valueObject));
        $this->assertEquals($valueObject, $normalizer->denormalize($input, IntegerExample::class));
    }

}

class IntegerExample implements IntegerValue
{

    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}