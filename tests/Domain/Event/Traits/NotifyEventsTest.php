<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Event\Traits;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEventDispatcher;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\DomainEventsCollector;
use PlanB\Domain\Event\Traits\NotifyEvents;
use Prophecy\PhpUnit\ProphecyTrait;

final class NotifyEventsTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_dispatch_an_event_properly()
    {
        $event     = $this->prophesize(DomainEventInterface::class)->reveal();
        $collector = $this->prophesize(DomainEventsCollector::class);
        $collector->collect($event)
            ->shouldBeCalled();

        DomainEventDispatcher::instance()->setEventsCollector($collector->reveal());

        $sut = new NotifyEventsClass();
        $sut->dispatchThisEvent($event);
    }

}

class NotifyEventsClass
{
    use NotifyEvents;

    public function dispatchThisEvent(DomainEventInterface $event)
    {
        $this->notify($event);
    }

}
