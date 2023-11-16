<?php

namespace PlanB\Tests\Domain\Criteria;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\Operator;

class FilterTest extends TestCase
{
    public function test_it_manages_data_properly()
    {
        $filter = new Filter('field', Operator::NOT_CONTAINS, 'value');

        $this->assertEquals('field', $filter->getField());
        $this->assertEquals(Operator::NOT_CONTAINS, $filter->getOperator());
        $this->assertEquals('value', $filter->getValue());
    }
}
