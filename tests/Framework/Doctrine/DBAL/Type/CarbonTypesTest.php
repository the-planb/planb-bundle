<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Doctrine\DBAL\Type;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonDateImmutableType;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonDateTimeImmutableType;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonDateTimeType;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonDateType;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonTimeImmutableType;
use PlanB\Framework\Doctrine\DBAL\Type\CarbonTimeType;
use Prophecy\PhpUnit\ProphecyTrait;

final class CarbonTypesTest extends TestCase
{
    use ProphecyTrait;


    #[DataProvider('CarbonTypeProvider')]
    public function test_it_converts_to_php_value_properly($name, $type, $expected, $good, $bad)
    {
        $platform = new MySQL80Platform();

        $this->assertInstanceOf($expected, $type->convertToPHPValue($good, $platform));
        $this->assertNull($type->convertToPHPValue(null, $platform));

        $this->assertTrue($type->requiresSQLCommentHint($platform));
        $this->assertSame($name, $type->getName());


        $this->expectException(ConversionException::class);
        $type->convertToPHPValue($bad, $platform);
    }

    public static function CarbonTypeProvider(): array
    {
        $time = '12:12:12';
        $date = '2012-03-01';
        $dateTime = '2012-03-01 12:12:12';
        $bad = '2012/03/01 12h12m12s';

        return [
            ['time_immutable', new CarbonTimeImmutableType(), CarbonImmutable::class, $time, $dateTime],
            ['date_immutable', new CarbonDateImmutableType(), CarbonImmutable::class, $date, $dateTime],
            ['datetime_immutable', new CarbonDateTimeImmutableType(), CarbonImmutable::class, $dateTime, $bad],
            ['time', new CarbonTimeType(), Carbon::class, $time, $dateTime],
            ['date', new CarbonDateType(), Carbon::class, $date, $dateTime],
            ['datetime', new CarbonDateTimeType(), Carbon::class, $dateTime, $bad],
        ];
    }
}
