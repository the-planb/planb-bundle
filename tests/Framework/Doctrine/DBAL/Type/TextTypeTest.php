<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\TextType;
use PlanB\Type\StringValue;

final class TextTypeTest extends TestCase
{

    public function test_it_manages_data_properly()
    {
        $platform = new MySQL80Platform();
        $value = "cadena";
        $type = $this->makeType();
        $instance = new TextExample($value);

        $this->assertEquals($instance, $type->convertToPHPValue($value, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($instance, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($value, $platform));
        $this->assertSame('LONGTEXT', $type->getSQLDeclaration([], $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame('TextExample', $type->getName());
    }


    private function makeType()
    {
        return new class extends TextType {

            public function getFQN(): string
            {
                return TextExample::class;
            }

            public function getName(): string
            {
                return 'TextExample';
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

class TextExample implements StringValue
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
