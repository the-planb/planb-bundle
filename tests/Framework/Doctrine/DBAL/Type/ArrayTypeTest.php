<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\ArrayType;
use PlanB\Type\ArrayValue;

final class ArrayTypeTest extends TestCase
{
    public function test_it_manages_data_properly()
    {
        $platform = new MySQL80Platform();
        $input = ['a', 'b', 'c'];
        $value = json_encode($input, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);

        $type = $this->makeType();
        $instance = new ArrayExample($input);

        $this->assertEquals($instance, $type->convertToPHPValue($value, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($instance, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($input, $platform));
        $this->assertSame('JSON', $type->getSQLDeclaration([], $platform));
        $this->assertFalse($type->requiresSQLCommentHint($platform));
        $this->assertSame('ArrayExample', $type->getName());

        $this->assertNull($type->convertToPHPValue(null, $platform));
        $this->assertNull($type->convertToDatabaseValue(null, $platform));
    }

    private function makeType()
    {
        return new class () extends ArrayType {
            public function getFQN(): string
            {
                return ArrayExample::class;
            }

            public function getName(): string
            {
                return 'ArrayExample';
            }
        };
    }

    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_database_value()
    {
        $platform = new MySQL80Platform();
        $value = new \stdClass();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($value, $platform);
    }

    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value_to_php_value()
    {
        $platform = new MySQL80Platform();
        $value = new \stdClass();
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }


    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_json_array_php_value()
    {
        $platform = new MySQL80Platform();
        $value = json_encode("NO ES EL ARRAY DE UN JSON");
        $type = $this->makeType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($value, $platform);
    }
}

class ArrayExample implements ArrayValue
{
    private array $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
