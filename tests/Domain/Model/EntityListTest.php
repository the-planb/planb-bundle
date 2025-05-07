<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityId;
use PlanB\Domain\Model\EntityList;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Uid\Ulid;

class EntityListTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_uses_id_as_key()
    {
        $key1 = (new Ulid())->toRfc4122();
        $key2 = (new Ulid())->toRfc4122();
        $key3 = (new Ulid())->toRfc4122();

        $entityList = EntityList::collect([
            $this->createEntity($key1),
            $this->createEntity($key2),
            $this->createEntity($key3),
        ])->toArray();

        $this->assertArrayHasKey($key1, $entityList);
        $this->assertArrayHasKey($key2, $entityList);
        $this->assertArrayHasKey($key3, $entityList);
        $this->assertCount(3, $entityList);
    }

    private function createEntity(string $ulid): Entity
    {
        $entityId = new class ($ulid) extends EntityId {
        };

        $entity = $this->prophesize(Entity::class);
        $entity->getId()->willReturn($entityId);

        return $entity->reveal();
    }
}
