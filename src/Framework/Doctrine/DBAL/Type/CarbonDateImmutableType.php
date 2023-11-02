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

use Carbon\CarbonImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateImmutableType;

final class CarbonDateImmutableType extends DateImmutableType
{
    private const NAME = 'date_immutable';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CarbonImmutable
    {
        if (is_null($value)) {
            return null;
        }
        $result = parent::convertToPHPValue($value, $platform);

        if ($result instanceof DateTimeInterface) {
            $result = CarbonImmutable::instance($result);
        }

        return $result;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
