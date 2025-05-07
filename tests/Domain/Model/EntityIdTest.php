<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Model;

use PlanB\Domain\Model\EntityId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;

class MyEntityId extends EntityId
{
}

final class EntityIdTest extends TestCase
{
    public function test_it_can_be_instantiated_without_value()
    {
        $entityId = new MyEntityId();
        $this->assertInstanceOf(Ulid::class, $entityId->ulid());
    }

    public function test_it_can_be_instantiated_with_a_value()
    {
        $ulid = (string)(new Ulid());

        $entityId = new MyEntityId($ulid);

        $this->assertInstanceOf(Ulid::class, $entityId->ulid());
        $this->assertSame($ulid, (string)$entityId->ulid());
    }

    public function test_it_recognizes_two_equals_entity_id()
    {
        $ulid = (string)(new Ulid());

        $first  = new MyEntityId($ulid);
        $second = new MyEntityId($ulid);

        $this->assertTrue($first->equals($second));
    }


    public function test_it_recognizes_two_differents_entity_id()
    {
        $first  = new MyEntityId();
        $second = new MyEntityId();

        $this->assertFalse($first->equals($second));
    }

    public function test_it_retuns_its_uid_in_rfc4122_format()
    {
        $entityId = new MyEntityId('01FZWQRQ7NVFAE9FK7NWP2B65A');
        $this->assertSame('017ff97c-5cf5-dbd4-e4be-67af2c2598aa', $entityId->__toString());
    }

}
