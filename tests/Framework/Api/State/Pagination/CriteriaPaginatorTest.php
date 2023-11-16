<?php

namespace PlanB\Tests\Framework\Api\State\Pagination;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\State\Pagination\CriteriaPaginator;

class CriteriaPaginatorTest extends TestCase
{
    public function test_it_manage_data_properly()
    {

        $data = array_fill(0, 10, 'row');

        $paginator = new CriteriaPaginator($data, 2, 10, 160);

        $this->assertEquals(10, $paginator->count());
        $this->assertEquals(16, $paginator->getLastPage());
        $this->assertEquals(160, $paginator->getTotalItems());
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertEquals(10, $paginator->getItemsPerPage());
        $this->assertEquals($data, iterable_to_array($paginator->getIterator()));
    }

    public function test_it_manage_data_properly_when_page_size_is_zero()
    {

        $data = array_fill(0, 10, 'row');

        $paginator = new CriteriaPaginator($data, 2, 0, 160);

        $this->assertEquals(10, $paginator->count());
        $this->assertEquals(1, $paginator->getLastPage());
        $this->assertEquals(160, $paginator->getTotalItems());
        $this->assertEquals(2, $paginator->getCurrentPage());
        $this->assertEquals(0, $paginator->getItemsPerPage());
        $this->assertEquals($data, iterable_to_array($paginator->getIterator()));
    }
}
