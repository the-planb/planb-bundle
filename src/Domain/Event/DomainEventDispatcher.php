<?php

/**
 * This file is part of the planb project.
 *
 * (c) jmpantoja <jmpantoja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PlanB\Domain\Event;

use BadMethodCallException;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class DomainEventDispatcher extends EventDispatcher
{
    protected static ?DomainEventDispatcher $instance = null;
    private DomainEventsCollector $eventsCollector;

    final private function __construct()
    {
        parent::__construct();
        $this->eventsCollector = new DomainEventsCollector();
    }

    public static function instance(): self
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function getEventsCollector(): DomainEventsCollector
    {
        return $this->eventsCollector;
    }

    public function setEventsCollector(DomainEventsCollector $eventsCollector): void
    {
        $this->eventsCollector = $eventsCollector;
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        if ($event instanceof DomainEventInterface) {
            $this->eventsCollector->collect($event);
        }

        return parent::dispatch($event, $eventName);
    }

    public function __clone()
    {
        throw new BadMethodCallException('Este objeto no puede ser clonado');
    }

    public function __wakeup()
    {
        throw new BadMethodCallException('Este objeto no puede ser deserializado');
    }
}
