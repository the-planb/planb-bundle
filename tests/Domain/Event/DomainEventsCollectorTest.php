<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Event;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\DomainEventsCollector;
use Prophecy\PhpUnit\ProphecyTrait;

final class DomainEventsCollectorTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_can_collect_some_events()
    {
        $collector = new DomainEventsCollector();

        $events = $this->give_me_a_list_of_events(3);
        foreach ($events as $event) {
            $collector->collect($event);
        }

        $this->assertSame($events, $collector->flushEvents());
        $this->assertSame([], $collector->flushEvents());
    }

    private function give_me_a_list_of_events(int $length)
    {
        $events = [];
        for ($i = 0; $i < $length; $i++) {
            $events[] = $this->prophesize(DomainEventInterface::class)->reveal();
        }

        return $events;
    }

    public function test_it_can_clear_the_events_collection()
    {
        $collector = new DomainEventsCollector();

        $events = $this->give_me_a_list_of_events(3);
        foreach ($events as $event) {
            $collector->collect($event);
        }

        $collector->clear();
        $this->assertSame([], $collector->flushEvents());
    }
}
