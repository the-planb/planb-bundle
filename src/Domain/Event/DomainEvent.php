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

use Carbon\CarbonImmutable;

abstract class DomainEvent implements DomainEventInterface
{
    private CarbonImmutable $when;
    private object $event;

    public function __construct(object $event, CarbonImmutable $when = null)
    {
        $this->event = $event;
        $this->when  = $when ?? CarbonImmutable::now();
    }

    public function when(): CarbonImmutable
    {
        return $this->when;
    }

    public function jsonSerialize(): mixed
    {
        return $this->event;
    }
}
