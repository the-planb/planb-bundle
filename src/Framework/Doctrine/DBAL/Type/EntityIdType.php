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
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use Symfony\Component\Uid\Ulid;
use PlanB\Domain\Model\EntityId;
use function is_string;

abstract class EntityIdType extends Type
{
    public const NAME = 'EntityId';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        if ($platform->hasNativeGuidType()) {
            return $platform->getGuidTypeDeclarationSQL($column);
        }

        return $platform->getBinaryTypeDeclarationSQL([
            'length' => '16',
            'fixed'  => true,
        ]);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof EntityId || null === $value) {
            return $value;
        }

        if (! is_string($value)) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', 'string', EntityId::class]
            );
        }

        try {
            return $this->makeFromValue($value);
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, $this->getName(), $e);
        }
    }

    abstract public function makeFromValue(string $value): EntityId;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $toString = $platform->hasNativeGuidType() ? 'toRfc4122' : 'toBinary';

        if ($value instanceof EntityId) {
            return $value->ulid()->$toString();
        }

        if (null === $value || '' === $value) {
            return null;
        }

        if (! is_string($value)) {
            throw ConversionException::conversionFailedInvalidType(
                $value,
                $this->getName(),
                ['null', 'string', EntityId::class]
            );
        }

        try {
            return Ulid::fromString($value)->$toString();
        } catch (InvalidArgumentException $e) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
