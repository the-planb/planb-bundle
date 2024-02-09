<?php

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\Entity;
use PlanB\Framework\Api\Normalizer\EntityNormalizer;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_only_supports_entityIds()
    {
        $normalizer = new EntityNormalizer();


        $this->assertFalse($normalizer->supportsDenormalization(['id' => 'data'], Entity::class));
        $this->assertTrue($normalizer->supportsDenormalization(['@id' => 'data'], Entity::class));

    }

    public function test_it_denormalize_an_entityId_properly()
    {
        $serializer = $this->prophesize(DenormalizerInterface::class);
        $serializer->denormalize('data', Entity::class, Argument::cetera())
            ->willReturn(new \stdClass())
            ->shouldBeCalledOnce();

        $normalizer = new EntityNormalizer();
        $normalizer->setDenormalizer($serializer->reveal());

        $entity = $normalizer->denormalize(['@id' => 'data'], Entity::class);

        $this->assertEquals(new \stdClass(), $entity);
    }

    public function test_it_supports_types_works_properly()
    {
        $normalizer = new EntityNormalizer();
        $this->assertEquals([
            Entity::class => true
        ], $normalizer->getSupportedTypes('format'));
    }
}
