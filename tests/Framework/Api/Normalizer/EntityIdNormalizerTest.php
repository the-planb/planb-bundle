<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\Normalizer;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\EntityId;
use PlanB\Framework\Api\Normalizer\EntityIdNormalizer;
use Prophecy\PhpUnit\ProphecyTrait;

final class EntityIdNormalizerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_only_supports_entityIds()
    {
        $normalizer = new EntityIdNormalizer();
        $badEntityId = $this->prophesize()
            ->reveal();

        $entityId = $this->prophesize(EntityId::class)
            ->reveal();

        $this->assertFalse($normalizer->supportsNormalization($badEntityId));
        $this->assertTrue($normalizer->supportsNormalization($entityId));


        $this->assertFalse($normalizer->supportsDenormalization('data', $badEntityId::class));
        $this->assertTrue($normalizer->supportsDenormalization('data', $entityId::class));

    }

    public function test_it_normalize_an_entityId_properly()
    {
        $normalizer = new EntityIdNormalizer();

        $entityId = $this->prophesize(EntityId::class);
        $entityId->__toString()
            ->willReturn('output');

        $this->assertSame('output', $normalizer->normalize($entityId->reveal()));
    }

    public function test_it_denormalize_an_entityId_properly()
    {
        $normalizer = new EntityIdNormalizer();

        $ulid = '018d2867-f075-2519-d0c8-c4614ff459d7';
        $entityId = $normalizer->denormalize($ulid, FakeId::class);

        $this->assertSame($ulid, (string)$entityId);
        $this->assertInstanceOf(FakeId::class, $entityId);
    }

    public function test_it_supports_types_works_properly()
    {
        $normalizer = new EntityIdNormalizer();
        $this->assertEquals([
            '*' => false,
            EntityId::class => true,
        ], $normalizer->getSupportedTypes('format'));
    }

}

class FakeId extends EntityId
{
}
