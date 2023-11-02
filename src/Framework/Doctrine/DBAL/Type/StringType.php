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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PlanB\Type\StringValue;

abstract class StringType extends ValueObjectType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?StringValue
    {
        if (is_null($value)) {
            return null;
        }
        $type = $this->getFQN();
        try {
            return new $type($value);
        } catch (\Throwable) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $type,
                ['string']
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        if ($value instanceof StringValue) {
            return $value->__toString();
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getFQN(),
            [\Stringable::class, 'string']
        );
    }
}
