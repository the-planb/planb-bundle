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
use PlanB\Type\FloatValue;

abstract class FloatType extends ValueObjectType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getFloatDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FloatValue
    {
        if (is_null($value)) {
            return null;
        }
        $value = $this->likeFloat($value);
        $type = $this->getFQN();
        try {
            return new $type($value);
        } catch (\Throwable) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $type,
                ['float']
            );
        }
    }

    private function likeFloat(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        if ((string)(float)$value === $value) {
            return (float)$value;
        }
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?float
    {
        if (is_null($value)) {
            return null;
        }

        $value = $this->likeFloat($value);
        if (is_float($value)) {
            return $value;
        }

        if ($value instanceof FloatValue) {
            return $value->toFloat();
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getFQN(),
            [FloatValue::class, 'float']
        );
    }
}
