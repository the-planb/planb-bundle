<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\Normalizer\ArrayValueNormalizer;
use PlanB\Type\ArrayValue;
use Prophecy\PhpUnit\ProphecyTrait;

final class ArrayObjectNormalizerTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_only_normalizes_objects_that_implements_the_right_interface()
    {
        $good = $this->prophesize(ArrayValueExample::class)->reveal();
        $bad = $this->prophesize()->reveal();

        $normalizer = new ArrayValueNormalizer();

        $this->assertTrue($normalizer->supportsNormalization($good));
        $this->assertFalse($normalizer->supportsNormalization($bad));
    }

    public function test_it_only_denormalizes_values_of_the_right_type()
    {
        $good = ['a', 'b', 'c'];
        $bad = 'its not an array';

        $normalizer = new ArrayValueNormalizer();

        $this->assertTrue($normalizer->supportsDenormalization($good, ArrayValueExample::class));
        $this->assertFalse($normalizer->supportsDenormalization($bad, ArrayValueExample::class));
    }

    public function test_it_normalizes_and_denormalizes_properly()
    {
        $input = ['a', 'b', 'c'];
        $valueObject = new ArrayValueExample($input);

        $normalizer = new ArrayValueNormalizer();
        $this->assertEquals($input, $normalizer->normalize($valueObject));
        $this->assertEquals($valueObject, $normalizer->denormalize($input, ArrayValueExample::class));
    }

    public function test_it_supports_types_works_properly()
    {
        $normalizer = new ArrayValueNormalizer();
        $this->assertEquals([
            '*' => false,
            ArrayValue::class => true
        ], $normalizer->getSupportedTypes('format'));
    }

}

class ArrayValueExample implements ArrayValue
{

    private array $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}