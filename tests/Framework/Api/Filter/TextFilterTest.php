<?php

namespace PlanB\Tests\Framework\Api\Filter;

use PHPUnit\Framework\TestCase;
use PlanB\Tests\Framework\Api\Filter\Traits\FilterTrait;

class TextFilterTest extends TestCase
{
    use FilterTrait;

    /**
     * @dataProvider fiterPropertyProvider
     */
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

    public function fiterPropertyProvider()
    {
        return [
            ['exact', 'hola', "LOWER(A.uno) LIKE 'hola'"],
            ['partial', 'hola', "LOWER(A.uno) LIKE '%hola%'"],
            ['start', 'hola', "LOWER(A.uno) LIKE 'hola%'"],
            ['end', 'hola', "LOWER(A.uno) LIKE '%hola'"],
        ];
    }

    /**
     * @dataProvider fiterPropertyProvider
     */
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

    /**
     * @dataProvider fiterPropertyProvider
     */
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
