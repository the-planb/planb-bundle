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
use PlanB\Type\IntegerValue;

abstract class IntegerType extends ValueObjectType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): IntegerValue
    {
        $type = $this->getFQN();
        try {
            return new $type($value);
        } catch (\Throwable) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $type,
                ['int']
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        if (is_int($value)) {
            return $value;
        }

        if ($value instanceof IntegerValue) {
            return $value->toInt();
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getFQN(),
            [IntegerValue::class, 'int']
        );
    }
}
