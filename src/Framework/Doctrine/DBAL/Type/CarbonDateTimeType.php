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

use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

final class CarbonDateTimeType extends DateTimeType
{
    private const NAME = 'datetime';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Carbon
    {
        if (is_null($value)) {
            return null;
        }
        $result = parent::convertToPHPValue($value, $platform);
        if ($result instanceof DateTimeInterface) {
            $result = Carbon::instance($result);
        }

        return $result;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
