<?php

namespace PlanB\Tests\Domain\Criteria;


use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Pagination;

class PaginationTest extends TestCase
{
    public function test_it_manages_data_properly()
    {
        $pagination = new Pagination(4, 10);

        $this->assertEquals(4, $pagination->getCurrentPage());
        $this->assertEquals(30, $pagination->getFirstResult());
        $this->assertEquals(10, $pagination->getMaxResults());
    }

    public function test_it_manages_default_values_properly()
    {
        $pagination = new Pagination();

        $this->assertEquals(1, $pagination->getCurrentPage());
        $this->assertEquals(0, $pagination->getFirstResult());
        $this->assertEquals(10, $pagination->getMaxResults());
    }
}
