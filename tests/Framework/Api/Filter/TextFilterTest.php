<?php

namespace PlanB\Tests\Framework\Api\Filter;

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PlanB\Tests\Framework\Api\Filter\Traits\FilterTrait;

class TextFilterTest extends TestCase
{
    use FilterTrait;

    #[DataProvider('fiterPropertyProvider')]
    public function testFilterProperty(string $strategy, string $value, string $expected)
    {
        $queryBuilder = $this->giveMeAQueryBuilderThatAddWhere($expected);
        $queryNameGenerator = $this->giveMeANameGenerator();

        $textFilter = $this->giveMeATextFilter()
            ->withPropertyNames(['uno'])
            ->please();

        $textFilter->apply($queryBuilder, $queryNameGenerator, 'resourceClass', null, [
            'filters' => [
                'uno' => [$strategy => $value],
            ]
        ]);
    }


    public function testThrowsAnExceptionOnInvalidStrategy()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('strategy bad-strategy does not exist.');

        $queryBuilder = $this->giveMeAQueryBuilder();
        $queryNameGenerator = $this->giveMeANameGenerator();

        $textFilter = $this->giveMeATextFilter()
            ->withPropertyNames(['uno'])
            ->please();

        $textFilter->apply($queryBuilder, $queryNameGenerator, 'resourceClass', null, [
            'filters' => [
                'uno' => ['bad-strategy' => 'any-value'],
            ]
        ]);
    }

    public static function fiterPropertyProvider(): array
    {
        return [
            ['exact', 'hola', "LOWER(A.uno) LIKE 'hola'"],
            ['partial', 'hola', "LOWER(A.uno) LIKE '%hola%'"],
            ['start', 'hola', "LOWER(A.uno) LIKE 'hola%'"],
            ['end', 'hola', "LOWER(A.uno) LIKE '%hola'"],
        ];
    }

    #[DataProvider('fiterPropertyProvider')]
    public function testFilterDisabledProperty(string $strategy, string $value)
    {
        $queryBuilder = $this->giveMeAQueryBuilderThatNeverChange();
        $queryNameGenerator = $this->giveMeANameGenerator();

        $textFilter = $this->giveMeATextFilter()
            ->withPropertyNames(['uno'])
            ->please();

        $textFilter->apply($queryBuilder, $queryNameGenerator, 'resourceClass', null, [
            'filters' => [
                'dos' => [$strategy => $value],
            ]
        ]);
    }

    #[DataProvider('fiterPropertyProvider')]
    public function testFilterMissingProperty(string $strategy, string $value)
    {
        $queryBuilder = $this->giveMeAQueryBuilderThatNeverChange();
        $queryNameGenerator = $this->giveMeANameGenerator();

        $textFilter = $this->giveMeATextFilter()
            ->withPropertyNames(['uno'])
            ->please();

        $textFilter->apply($queryBuilder, $queryNameGenerator, 'resourceClass', null, [
            'filters' => [
                'XXX' => [$strategy => $value],
            ]
        ]);

    }


    public function testDescription()
    {
        $textFilter = $this->giveMeATextFilter()
            ->withPropertyNames(['uno', 'dos'])
            ->please();

        $description = $textFilter->getDescription('resourceClass');

        $keys = ['uno[exact]', 'uno[partial]', 'uno[start]', 'uno[end]'];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $description);
            $this->assertEquals([
                "property" => "uno",
                "type" => "string",
                "required" => false
            ], $description[$key]);
        }

        $keys = ['dos[exact]', 'dos[partial]', 'dos[start]', 'dos[end]'];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $description);
            $this->assertEquals([
                "property" => "dos",
                "type" => "string",
                "required" => false
            ], $description[$key]);
        }
    }

}
