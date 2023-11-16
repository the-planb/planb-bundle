<?php

namespace PlanB\Tests\Framework\Doctrine\Criteria;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Operator;
use PlanB\Framework\Doctrine\Criteria\InvalidFilterException;

class InvalidFilterExceptionTest extends TestCase
{
    public function test_it_can_be_initialized()
    {
        $exception = InvalidFilterException::make(Operator::EQUALS, 'field');

        $this->assertEquals("Filter of type 'equals' is not applicable to field 'field'", $exception->getMessage());
    }
}
