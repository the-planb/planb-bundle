<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\BooleanType;
use PlanB\Type\BooleanValue;

final class BooleanTypeTest extends TestCase
{
    public function test_it_manages_data_properly()
    {
        $platform = new MySQL80Platform();
        $value = true;
        $type = $this->makeType();
        $instance = new BooleanExample($value);

        $this->assertEquals($instance, $type->convertToPHPValue($value, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($instance, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($value, $platform));
        $this->assertSame('TINYINT(1)', $type->getSQLDeclaration([], $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame('BooleanExample', $type->getName());

        $this->assertNull($type->convertToPHPValue(null, $platform));

        $this->assertNull($type->convertToDatabaseValue(null, $platform));
    }

    private function makeType()
    {
        return new class () extends BooleanType {
            public function getFQN(): string
            {
                return BooleanExample::class;
            }

            public function getName(): string
            {
                return 'BooleanExample';
            }
        };
    }

    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_php_value()
    {
        $platform = new MySQL80Platform();
        $value = new \stdClass();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }


    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_database_value()
    {
        $platform = new MySQL80Platform();
        $value = new \stdClass();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }
}

class BooleanExample implements BooleanValue
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function toBoolean(): bool
    {
        return $this->value;
    }
}
