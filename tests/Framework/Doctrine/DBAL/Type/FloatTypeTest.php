<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\FloatType;
use PlanB\Type\FloatValue;

final class FloatTypeTest extends TestCase
{

    private function makeType()
    {
        return new class extends FloatType {

            public function getFQN(): string
            {
                return FloatExample::class;
            }

            public function getName(): string
            {
                return 'FloatExample';
            }
        };
    }

    public function test_it_is_configured_properly()
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $this->assertSame('DOUBLE PRECISION', $type->getSQLDeclaration([], $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame('FloatExample', $type->getName());
    }

    /**
     * @dataProvider toPhpValues
     */
    public function test_it_converts_to_php_value_properly($value, $instance)
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $this->assertEquals($instance, $type->convertToPHPValue($value, $platform));
    }

    public function toPhpValues()
    {
        return [
            [10.1, new FloatExample(10.1)],
            ["101.1", new FloatExample(101.1)],
            ["12", new FloatExample(12)],
            [12, new FloatExample(12)],
            [null, null]
        ];
    }

    public function test_it_converts_to_database_value_properly()
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $value = 10.1;
        $instance = new FloatExample($value);

        $this->assertSame($value, $type->convertToDatabaseValue($instance, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($value, $platform));

        $this->assertNull($type->convertToDatabaseValue(null, $platform));
    }

    /**
     * @dataProvider badValues
     */
    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_php_value($value)
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }

    /**
     * @dataProvider badValues
     */
    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_database_value($value)
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }

    public function badValues(): array
    {
        return [
            [new \stdClass()],
            [""],
            ["cadena"],
            ["12.0.1"],
        ];
    }
}

class FloatExample implements FloatValue
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }
}
