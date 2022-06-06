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
use PlanB\Type\ArrayValue;

abstract class ArrayType extends ValueObjectType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ArrayValue
    {
        $type = $this->getFQN();

        try {
            $data = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            throw ConversionException::conversionFailedSerialization($value, 'json', $e->getMessage(), $e);
        }

        try {
            return new $type($data);
        } catch (\Throwable) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $type,
                ['json string']
            );
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (is_array($value) or $value instanceof \JsonSerializable) {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        }

        if ($value instanceof ArrayValue) {
            return json_encode($value->toArray(), JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getFQN(),
            ['array', ArrayValue::class, \JsonSerializable::class]
        );
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return ! $platform->hasNativeJsonType();
    }
}
