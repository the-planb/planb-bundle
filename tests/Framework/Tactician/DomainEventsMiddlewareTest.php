<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Tactician;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEventDispatcher;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\DomainEventsCollector;
use PlanB\Domain\Event\EventStore;
use PlanB\Framework\Tactician\DomainEventsMiddleware;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class DomainEventsMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_executes_properly()
    {
        $events     = $this->give_me_an_event_list(3);
        $repository = $this->give_me_a_repository_that_expects_events($events);
        $this->give_me_a_dispatcher_that_emit_events($events);

        $middleware = new DomainEventsMiddleware($repository);
        $response   = $middleware->execute(new \stdClass(), fn () => 'response');

        $this->assertSame('response', $response);
    }

    private function give_me_an_event_list(int $length = 0)
    {
        $events = [];
        for ($i = 0; $i < $length; $i++) {
            $events[] = $this->prophesize(DomainEventInterface::class)->reveal();
        }

        return $events;
    }

    private function give_me_a_repository_that_expects_events(array $events): EventStore
    {
        $repository = $this->prophesize(EventStore::class);
        $repository->persist(Argument::type(DomainEventInterface::class))
            ->shouldBeCalledTimes(count($events));

        return $repository->reveal();
    }

    private function give_me_a_dispatcher_that_emit_events(array $events): void
    {
        $collector = $this->prophesize(DomainEventsCollector::class);
        $collector->flushEvents()->willReturn($events);

        DomainEventDispatcher::instance()
            ->setEventsCollector($collector->reveal());
    }
}
