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

namespace PlanB\Framework\Tactician;

use League\Tactician\Middleware;
use PlanB\Domain\Event\DomainEventDispatcher;
use PlanB\Domain\Event\EventStore;

final class DomainEventsMiddleware implements Middleware
{
    private EventStore $repository;

    public function __construct(EventStore $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute($command, callable $next)
    {
        $eventDispatcher = DomainEventDispatcher::instance();
        $eventsCollector = $eventDispatcher->getEventsCollector();

        $response = $next($command);
        $events   = $eventsCollector->flushEvents();

        foreach ($events as $event) {
            $this->repository->persist($event);
        }

        return $response;
    }
}
