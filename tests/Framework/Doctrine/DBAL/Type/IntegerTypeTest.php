<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\IntegerType;
use PlanB\Type\IntegerValue;

final class IntegerTypeTest extends TestCase
{

    private function makeType()
    {
        return new class extends IntegerType {

            public function getFQN(): string
            {
                return IntegerExample::class;
            }

            public function getName(): string
            {
                return 'IntegerExample';
            }
        };
    }

    public function test_it_is_configured_properly()
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $this->assertSame('INT', $type->getSQLDeclaration([], $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame('IntegerExample', $type->getName());
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
            [10, new IntegerExample(10)],
            ["101", new IntegerExample(101)],
            [null, null],
        ];
    }

    public function test_it_converts_to_database_value_properly()
    {
        $platform = new MySQL80Platform();
        $type = $this->makeType();

        $value = 10;
        $instance = new IntegerExample($value);

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
            ["12.0"],
        ];
    }


}

class IntegerExample implements IntegerValue
{
    private int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
