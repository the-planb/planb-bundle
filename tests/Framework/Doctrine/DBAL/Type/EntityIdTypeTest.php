<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\EntityId;
use PlanB\Framework\Doctrine\DBAL\Type\EntityIdType;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Uid\Ulid;

class MyEntityId extends EntityId
{

}

class MyEntityIdType extends EntityIdType
{

    public function makeFromValue(string $value): EntityId
    {
        return new MyEntityId($value);
    }

    public function getName()
    {
    }
}

final class EntityIdTypeTest extends TestCase
{

    use ProphecyTrait;

    public function test_it_returns_the_correct_sql_declaration()
    {
        $type = new MyEntityIdType();

        $platform = new MySQL80Platform();
        $this->assertSame('BINARY(16)', $type->getSQLDeclaration([], $platform));

        $platform = new PostgreSQLPlatform();
        $this->assertSame('UUID', $type->getSQLDeclaration([], $platform));
    }

    /**
     * @dataProvider valuesProvider
     */
    public function test_it_converts_to_php_value_properly($ulid)
    {
        $platform = new MySQL80Platform();
        $type     = new MyEntityIdType();

        $this->assertInstanceOf(MyEntityId::class, $type->convertToPHPValue($ulid, $platform));
    }

    /** @dataProvider badValuesProvider */
    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_value($ulid)
    {
        $platform = new MySQL80Platform();
        $type     = new MyEntityIdType();

        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($ulid, $platform);
    }

    /**
     * @dataProvider toDatabaseProvider
     */
    public function test_it_converts_to_database_value_properly($entityId, $expected)
    {
        $platform = new MySQL80Platform();
        $type     = new MyEntityIdType();

        $this->assertSame($expected, $type->convertToDatabaseValue($entityId, $platform));
        $this->assertTrue($type->requiresSQLCommentHint($platform));
    }


    /** @dataProvider badToDatabaseProvider */
    public function test_it_throws_an_exception_when_try_to_convert_an_invalid_entityId($badEntityId)
    {
        $platform = new MySQL80Platform();
        $type     = new MyEntityIdType();

        $this->expectException(ConversionException::class);
        $type->convertToDatabaseValue($badEntityId, $platform);
    }

    public function badValuesProvider()
    {
        return [
            ['bad-ulid'],
            [['not a string']],
        ];
    }

    public function valuesProvider()
    {
        $ulid = (string)(new Ulid());

        return [
            [$ulid],
            [new MyEntityId()],
        ];
    }

    public function toDatabaseProvider()
    {
        $ulid     = new Ulid();
        $entityId = new MyEntityId((string)$ulid);

        return [
            [$entityId, $ulid->toBinary()],
            [null, null],
            ['', null],
        ];
    }

    public function badToDatabaseProvider()
    {
        return [
            [new \stdClass()],
            ['bad-ulid'],
        ];
    }
}
