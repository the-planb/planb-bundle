<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Event;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\Event;
use PlanB\Domain\Event\EventId;

final class EventTest extends TestCase
{
    public function test_it_manages_the_data_properly()
    {
        $today = CarbonImmutable::now();
        $event = new Event('name', 'event', $today);
        $this->assertInstanceOf(EventId::class, $event->getId());
        $this->assertSame('name', $event->getName());
        $this->assertSame('event', $event->getEvent());
        $this->assertSame($today, $event->getDate());
    }

    public function test_it_manages_the_name_properly()
    {
        $name  = 'ESTO\\HAS\\BEEN\\WAS\\EVENT\\SPEC\\ENTITY\\DOCUMENT\\MODEL\\PHPCR'
            .'\\COUCHDOCUMENT\\DOMAIN\\DOCTRINE\\ORM\\MONGODB\\ES_UN_EVENTO\\COUCHDB';
        $today = CarbonImmutable::now();
        $event = new Event($name, 'event', $today);
        $this->assertSame('esto.es_un_evento', $event->getName());
    }

}
