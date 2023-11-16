<?php

namespace PlanB\Tests\Domain\Exception;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Exception\IncompleteMethodException;

class IncompleteMethodExceptionTest extends TestCase
{
    public function test_it_can_be_initialized_with_a_custom_message()
    {
        $exception = IncompleteMethodException::custom('custom message');

        $this->assertEquals("custom message", $exception->getMessage());
    }

    public function test_it_can_be_initialized_with_a_predefined_message()
    {
        $exception = IncompleteMethodException::fromMethod('__METHOD__');

        $expected = "The method '__METHOD__' has not been implemented yet.";

        $this->assertEquals($expected, $exception->getMessage());
    }
}
