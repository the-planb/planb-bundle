<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\EventId;
use PlanB\Framework\Doctrine\DBAL\Type\EventIdType;
use Symfony\Component\Uid\Ulid;

final class EventIdTypeTest extends TestCase
{

    public function test_it_can_create_a_eventId_from_a_value()
    {
        $type = new EventIdType();
        $ulid = new Ulid();

        $eventId = $type->makeFromValue((string)$ulid);

        $this->assertInstanceOf(EventId::class, $eventId);
        $this->assertEquals($ulid, $eventId->ulid());
        $this->assertSame('EventId', $type->getName());
    }
}
