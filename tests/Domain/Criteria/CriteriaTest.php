<?php

namespace PlanB\Tests\Domain\Criteria;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Criteria;
use PlanB\Domain\Criteria\Operator;
use PlanB\Domain\Criteria\Order;
use PlanB\Domain\Criteria\OrderDir;

class CriteriaTest extends TestCase
{
    public function test_it_manage_empty_data_properly()
    {
        $criteria = Criteria::empty();

        $order = $criteria->getOrder();
        $this->assertEquals(Order::empty(), $order);

        $pagination = $criteria->getPagination();
        $this->assertEquals(10, $pagination->getMaxResults());
        $this->assertEquals(1, $pagination->getCurrentPage());
        $this->assertEquals(0, $pagination->getFirstResult());

        $filters = $criteria->getFilters();
        $this->assertEmpty($filters);
    }

    public function test_it_manage_data_properly()
    {
        $criteria = Criteria::fromValues([
            'page' => 4,
            'itemsPerPage' => 10,
            'order' => ['field' => 'asc'],
            'title' => ['contains' => 'name'],
            'summary' => ['contains' => 'sentence'],
        ]);

        $order = $criteria->getOrder();
        $this->assertEquals(OrderDir::ASC, $order->getType());
        $this->assertEquals('field', $order->getField());

        $pagination = $criteria->getPagination();
        $this->assertEquals(10, $pagination->getMaxResults());
        $this->assertEquals(4, $pagination->getCurrentPage());
        $this->assertEquals(30, $pagination->getFirstResult());

        $filters = $criteria->getFilters();

        $this->assertEquals('title', $filters->get(0)->getField());
        $this->assertEquals(Operator::CONTAINS, $filters->get(0)->getOperator());
        $this->assertEquals('name', $filters->get(0)->getValue());

        $this->assertEquals('summary', $filters->get(1)->getField());
        $this->assertEquals(Operator::CONTAINS, $filters->get(1)->getOperator());
        $this->assertEquals('sentence', $filters->get(1)->getValue());

    }
}
