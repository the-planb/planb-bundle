<?php

namespace PlanB\Framework\Api\State\Pagination;

use ApiPlatform\State\Pagination\PaginatorInterface;
use ArrayIterator;
use Traversable;

final class CriteriaPaginator implements \IteratorAggregate, PaginatorInterface
{
    private ArrayIterator $iterator;
    private int $currentPage;
    private int $itemsPerPage;
    private int $totalItems;

    public function __construct(array $data, int $currentPage, int $itemsPerPage, int $totalItems)
    {
        $this->iterator = new ArrayIterator($data);

        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
    }

    public function getIterator(): Traversable
    {
        return $this->iterator;
    }

    public function count(): int
    {
        return iterator_count($this->iterator);
    }

    public function getLastPage(): float
    {
        if (0 >= $this->itemsPerPage) {
            return 1.;
        }

        return ceil($this->totalItems / $this->itemsPerPage) ?: 1.;
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }
}
