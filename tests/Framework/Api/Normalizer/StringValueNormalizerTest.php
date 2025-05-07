<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\StringValueNormalizer;
use PlanB\Type\StringValue;
use Prophecy\PhpUnit\ProphecyTrait;

final class StringValueNormalizerTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_only_normalizes_objects_that_implements_the_right_interface()
    {
        $good = $this->prophesize(StringExample::class)->reveal();
        $bad = $this->prophesize()->reveal();

        $normalizer = new StringValueNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($good));
        $this->assertFalse($normalizer->supportsNormalization($bad));
    }

    public function test_it_only_denormalizes_values_of_the_right_type()
    {
        $good = 'string';
        $bad = ['its not a string'];

        $normalizer = new StringValueNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization($good, StringExample::class));
        $this->assertFalse($normalizer->supportsDenormalization($bad, StringExample::class));
    }

    public function test_it_normalizes_and_denormalizes_properly()
    {
        $input = 'response';
        $valueObject = new StringExample($input);

        $normalizer = new StringValueNormalizer();
        $this->assertEquals($input, $normalizer->normalize($valueObject));
        $this->assertEquals($valueObject, $normalizer->denormalize($input, StringExample::class));
    }

    public function test_it_supports_types_works_properly()
    {
        $normalizer = new StringValueNormalizer();
        $this->assertEquals([
            '*' => false,
            StringValue::class => true,
        ], $normalizer->getSupportedTypes('format'));
    }

}

class StringExample implements StringValue
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
