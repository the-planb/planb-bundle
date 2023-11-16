<?php

namespace PlanB\Tests\Domain\Criteria;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Order;
use PlanB\Domain\Criteria\OrderDir;

class OrderTest extends TestCase
{
    public function test_it_manages_data_properly()
    {
        $order = new Order('field', OrderDir::ASC);

        $this->assertEquals('field', $order->getField());
        $this->assertEquals(OrderDir::ASC, $order->getType());
        $this->assertFalse($order->isEmpty());
    }

    public function test_it_manages_data_properly_when_is_empty()
    {
        $order = Order::empty();

        $this->assertTrue($order->isEmpty());
    }
}
