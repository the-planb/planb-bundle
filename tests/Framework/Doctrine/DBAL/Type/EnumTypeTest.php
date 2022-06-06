<?php
declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\EnumType;

class EnumTypeTest extends TestCase
{

    public function test_it_manages_data_properly()
    {
        $platform = new MySQL80Platform();
        $value = 'uno';
        $type = $this->makeType();
        $instance = EnumExample::UNO;


        $this->assertEquals($instance, $type->convertToPHPValue($value, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($instance, $platform));
        $this->assertSame($value, $type->convertToDatabaseValue($value, $platform));
        $this->assertSame('VARCHAR(255)', $type->getSQLDeclaration([], $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame('EnumExample', $type->getName());

        $this->assertNull($type->convertToPHPValue(null, $platform));

        $this->assertNull($type->convertToDatabaseValue(null, $platform));
    }

    private function makeType()
    {
        return new class extends EnumType {

            public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
            {
                return $platform->getStringTypeDeclarationSQL($column);
            }

            public function getName(): string
            {
                return 'EnumExample';
            }

            public function getFQN(): string
            {
                return EnumExample::class;
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

enum EnumExample: string
{
    case UNO = 'uno';
}