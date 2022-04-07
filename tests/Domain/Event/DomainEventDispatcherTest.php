<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Event;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEventDispatcher;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\DomainEventsCollector;
use Prophecy\PhpUnit\ProphecyTrait;

final class DomainEventDispatcherTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_is_a_singleton()
    {
        $instance = DomainEventDispatcher::instance();
        $this->assertSame($instance, DomainEventDispatcher::instance());
    }

    public function test_it_cant_be_serialized()
    {
        $dispatcher = DomainEventDispatcher::instance();

        $this->expectException(\BadMethodCallException::class);
        $dispatcher->__wakeup();
    }

    public function test_it_cant_be_cloned()
    {
        $dispatcher = DomainEventDispatcher::instance();

        $this->expectException(\BadMethodCallException::class);
        clone $dispatcher;
    }

    public function test_it_can_change_of_events_collector()
    {
        $collector = $this->prophesize(DomainEventsCollector::class)
            ->reveal();

        $dispatcher = DomainEventDispatcher::instance();
        $original   = $dispatcher->getEventsCollector();

        $this->assertNotSame($collector, $dispatcher->getEventsCollector());

        $dispatcher->setEventsCollector($collector);
        $this->assertSame($collector, $dispatcher->getEventsCollector());
    }

    public function test_it_dispatch_an_event_properly()
    {
        $event       = $this->prophesize()->reveal();
        $domainEvent = $this->prophesize(DomainEventInterface::class)->reveal();

        $collector = $this->prophesize(DomainEventsCollector::class);
        $collector->collect($event)
            ->shouldNotBeCalled();

        $collector->collect($domainEvent)
            ->shouldBeCalled();

        $dispatcher = DomainEventDispatcher::instance();
        $dispatcher->setEventsCollector($collector->reveal());

        $dispatcher->dispatch($event);
        $dispatcher->dispatch($domainEvent);
    }

}
