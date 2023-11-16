<?php

namespace PlanB\Tests\Framework\Doctrine\Criteria;

use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\Operator;
use PlanB\Framework\Doctrine\Criteria\DefaultFilter;
use Prophecy\PhpUnit\ProphecyTrait;

class DefaultFilterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider dataProvider
     */
    public function test_it_applies_a_filter_properly(Filter $filter, string $expected)
    {
        $expr = new Expr();

        $default = new DefaultFilter();
        $sentence = $default->apply($expr, $filter, 'A');
        $this->assertEquals($expected, $sentence);

    }

    public function dataProvider()
    {
        return [
            [new Filter('name', Operator::EQUALS, 'value'), "LOWER(A.name) = 'value'"],
            [new Filter('name', Operator::NOT_EQUALS, 'value'), "NOT(LOWER(A.name) = 'value')"],
            [new Filter('name', Operator::CONTAINS, 'value'), "LOWER(A.name) LIKE '%value%'"],
            [new Filter('name', Operator::STARTS_WITH, 'value'), "LOWER(A.name) LIKE 'value%'"],
            [new Filter('name', Operator::ENDS_WITH, 'value'), "LOWER(A.name) LIKE '%value'"],
            [new Filter('name', Operator::GREATER_THAN, 'value'), "A.name > value"],
            [new Filter('name', Operator::LESS_THAN, 'value'), "A.name < value"],
            [new Filter('name', Operator::GREATER_OR_EQUALS_THAN, 'value'), "A.name >= value"],
            [new Filter('name', Operator::LESS_OR_EQUALS_THAN, 'value'), "A.name <= value"],
            [new Filter('name', Operator::BETWEEN, 'min..max'), "A.name BETWEEN min AND max"],
            [new Filter('name', Operator::IDENTITY, 'id'), "A.name = 'id'"],
            [new Filter('name', Operator::NOT_IDENTITY, 'id'), "NOT(A.name = 'id')"],
        ];
    }
}


