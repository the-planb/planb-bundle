<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Model\Repository;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\Repository\PaginatedResult;

final class PaginationResultTest extends TestCase
{
    public function test_it_can_be_created_and_access_properties(): void
    {
        $items = ['item1', 'item2'];
        $currentPage = 1;
        $itemsPerPage = 10;
        $totalItems = 20;

        $paginatedResult = new PaginatedResult($items, $currentPage, $itemsPerPage, $totalItems);

        $this->assertSame($items, $paginatedResult->getItems());
        $this->assertSame($currentPage, $paginatedResult->getCurrentPage());
        $this->assertSame($itemsPerPage, $paginatedResult->getItemsPerPage());
        $this->assertSame($totalItems, $paginatedResult->getTotalItems());
    }

    public function test_it_converts_iterable_to_internal_array(): void
    {
        $iterator = new \ArrayIterator(['a', 'b', 'c']);

        $paginatedResult = new PaginatedResult($iterator, 1, 10, 3);

        $this->assertSame(['a', 'b', 'c'], $paginatedResult->getItems());
    }
}
