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

namespace PlanB\Framework\Doctrine\DBAL\Type;

use PlanB\Domain\Event\EventId;
use PlanB\Domain\Model\EntityId;

final class EventIdType extends EntityIdType
{
    public function makeFromValue(string $value): EntityId
    {
        return new EventId($value);
    }

    public function getName()
    {
        return 'EventId';
    }
}
