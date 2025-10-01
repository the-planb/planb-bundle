<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Criteria;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\CriteriaBuilder;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\FilterList;
use PlanB\Domain\Criteria\Operator;
use PlanB\Domain\Criteria\Order;
use PlanB\Domain\Criteria\OrderDir;
use PlanB\Domain\Criteria\Pagination;

class CriteriaBuilderTest extends TestCase
{
    public function test_criteria_can_be_created_properly()
    {
        $criteria = CriteriaBuilder::make()
            ->withFilters([
                'name' => ['equals' => 'pepe'],
            ])
            ->addFilters(['age' => ['gt' => 10]])
            ->withOrder('name', 'asc')
            ->withPagination(5, 50)
            ->withLimit(20)
            ->build();

        $filters = $criteria->getFilters();
        $order = $criteria->getOrder();
        $pagination = $criteria->getPagination();
        $limit = $criteria->getLimit();

        $this->assertCount(2, $filters);

        $this->assertEquals(new Filter('name', Operator::EQUALS, 'pepe'), $filters->get(0));
        $this->assertEquals(new Filter('age', Operator::GREATER_THAN, 10), $filters->get(1));

        $this->assertEquals(new Order('name', OrderDir::ASC), $order);
        $this->assertEquals(new Pagination(5, 50), $pagination);
        $this->assertEquals(20, $limit);
    }

    public function test_criteria_is_created_with_default_values_properly()
    {
        $criteria = CriteriaBuilder::make()
            ->build();

        $filters = $criteria->getFilters();
        $order = $criteria->getOrder();
        $pagination = $criteria->getPagination();

        $this->assertEquals(FilterList::collect(), $filters);
        $this->assertEquals(Order::empty(), $order);
        $this->assertEquals(new Pagination(1, 10), $pagination);
    }

    public function test_criteria_is_created_from_array_propery()
    {
        $criteria = CriteriaBuilder::make([
            'name' => ['equals' => 'pepe'],
            'order' => [
                0 => [],
                'age' => 'desc'
            ],
            'page' => 5,
            'itemsPerPage' => 50
        ])
            ->build();

        $filters = $criteria->getFilters();
        $order = $criteria->getOrder();
        $pagination = $criteria->getPagination();

        $this->assertEquals(new Filter('name', Operator::EQUALS, 'pepe'), $filters->get(0));
        $this->assertEquals(new Order('age', OrderDir::DESC), $order);
        $this->assertEquals(new Pagination(5, 50), $pagination);
    }

    public function test_its_manage_order_properly()
    {
        $criteria = CriteriaBuilder::make()
            ->fromArray([
                'order' => [],
            ])
            ->build();

        $order = $criteria->getOrder();
        $this->assertEquals(Order::empty(), $order);

        $criteria = CriteriaBuilder::make()
            ->withOrder('age', OrderDir::DESC)
            ->build();

        $order = $criteria->getOrder();
        $this->assertEquals(new Order('age', OrderDir::DESC), $order);
    }

    public function test_its_manage_limit_properly()
    {
        $criteria = CriteriaBuilder::make();
        $this->assertEquals(10, $criteria->build()->getLimit());

        $criteria->withPagination(1, 20);
        $this->assertEquals(20, $criteria->build()->getLimit());


        $criteria->withLimit(30);
        $this->assertEquals(30, $criteria->build()->getLimit());
    }


    public function test_make_method_accepts_an_array()
    {
        $criteria = CriteriaBuilder::make([
            'name' => ['equals' => 'pepe'],
            'order' => ['age' => 'desc'],
            'page' => 5,
            'itemsPerPage' => 50
        ])
            ->build();

        $filters = $criteria->getFilters();
        $order = $criteria->getOrder();
        $pagination = $criteria->getPagination();

        $this->assertEquals(new Filter('name', Operator::EQUALS, 'pepe'), $filters->get(0));
        $this->assertEquals(new Order('age', OrderDir::DESC), $order);
        $this->assertEquals(new Pagination(5, 50), $pagination);
    }

    public function test_make_method_accepts_a_criteria_object()
    {
        $original = CriteriaBuilder::make([
            'name' => ['equals' => 'pepe'],
            'order' => ['age' => 'desc'],
            'page' => 5,
            'itemsPerPage' => 50
        ])
            ->build();

        $criteria = CriteriaBuilder::make($original)
            ->build();

        $filters = $criteria->getFilters();
        $order = $criteria->getOrder();
        $pagination = $criteria->getPagination();

        $this->assertEquals(new Filter('name', Operator::EQUALS, 'pepe'), $filters->get(0));
        $this->assertEquals(new Order('age', OrderDir::DESC), $order);
        $this->assertEquals(new Pagination(5, 50), $pagination);
    }
}
