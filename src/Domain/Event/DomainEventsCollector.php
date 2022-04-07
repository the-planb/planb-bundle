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

class DomainEventsCollector
{
    private array $events = [];

    public function collect(DomainEventInterface $event): self
    {
        $this->events[] = $event;

        return $this;
    }

    public function flushEvents(): array
    {
        $events = $this->events;
        $this->clear();

        return $events;
    }

    public function clear(): self
    {
        $this->events = [];

        return $this;
    }
}
