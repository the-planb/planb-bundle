<?php

declare(strict_types=1);

namespace PlanB\Framework\Doctrine\DBAL\Type;

use BackedEnum;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Throwable;
use UnitEnum;

abstract class EnumType extends ValueObjectType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (is_scalar($value)) {
            return $value;
        }

        if ($value instanceof UnitEnum) {
            return $value->value;
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getFQN(),
            [UnitEnum::class]
        );
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?BackedEnum
    {
        if (is_null($value)) {
            return null;
        }

        $type = $this->getFQN();
        try {
            return $type::from($value);
        } catch (Throwable) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $type,
                ['string']
            );
        }
    }
}
