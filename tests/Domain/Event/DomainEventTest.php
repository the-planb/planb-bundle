<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Event;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEvent;
use Prophecy\PhpUnit\ProphecyTrait;

final class DomainEventTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_can_be_created_properly()
    {
        $date  = CarbonImmutable::now();
        $event = $this->prophesize()->reveal();

        $domainEvent = new MyDomainEvent($event, $date);

        $this->assertSame($event, $domainEvent->jsonSerialize());
        $this->assertTrue($date->eq($domainEvent->when()));
    }
}

class MyDomainEvent extends DomainEvent
{

}
