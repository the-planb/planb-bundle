<?php

namespace PlanB\Tests\Framework\Api\Normalizer;

use App\BookStore\Domain\Model\Book;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\EntityListInput;
use PlanB\Framework\Api\Normalizer\EntityListNormalizer;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class EntityListNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function testItCanDenormalizeProperly()
    {
        $A = new \stdClass();
        $A->name = 'A';

        $B = new \stdClass();
        $B->name = 'B';


        $serializer = $this->prophesize(SerializerInterface::class)
            ->willImplement(DenormalizerInterface::class);

        $serializer->denormalize(Argument::type('string'), 'Entity', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($A);

        $serializer->denormalize(Argument::type('array'), 'EntityInput', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($B);


        $normalizer = new EntityListNormalizerExample($this->prophesize(...));
        $normalizer->setSerializer($serializer->reveal());


        $response = $normalizer->denormalize([
            ['@id' => 'iri'],
            ["name" => "B"]
        ], __CLASS__);


        $this->assertEquals([
            $A,
            ["name" => "B"]
        ], $response->toArray());


        $response = $normalizer->denormalize([
            "iri",
            ["name" => "B"]
        ], __CLASS__);

        $this->assertEquals([
            $A,
            ["name" => "B"]
        ], $response->toArray());


    }

    public function testItThrowAnExceptionWhenSerializerIsNotADenormalizer()
    {

        $message = preg_quote('The decorated normalizer must be an instance of "Symfony\Component\Serializer\Normalizer\DenormalizerInterface"');
        $this->expectExceptionMessageMatches("/{$message}/");
        $serializer = $this->prophesize(SerializerInterface::class);

        $normalizer = new EntityListNormalizerExample($this->prophesize(...));
        $normalizer->setSerializer($serializer->reveal());
    }
}

class EntityListNormalizerExample extends EntityListNormalizer
{
    /**
     * @var callable
     */
    private $prophesize;

    public function __construct(callable $prophesize)
    {
        $this->prophesize = $prophesize;
    }


    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return true;
    }

    protected function collect(array $data): EntityListInput
    {
        $entityInputList = ($this->prophesize)(EntityListInput::class);
        $entityInputList->toArray()
            ->willReturn($data);

        return $entityInputList->reveal();

    }

    protected function itemFromIri(string $input, mixed $format, array $context): object
    {
        return $this->convert($input, 'Entity', $format, $context);
    }

    protected function itemFromArray(array $input, mixed $format, array $context): object
    {
        return $this->convert($input, 'EntityInput', $format, $context);
    }
}