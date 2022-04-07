<?php

declare(strict_types=1);

namespace PlanB\Domain\Event;

use Carbon\CarbonImmutable;

interface DomainEventInterface extends \JsonSerializable
{
    public function when(): CarbonImmutable;
}
