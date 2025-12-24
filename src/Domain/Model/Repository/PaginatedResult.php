<?php

declare(strict_types=1);

namespace PlanB\Domain\Model\Repository;

final class PaginatedResult
{
    private array $items;
    private int $currentPage;
    private int $itemsPerPage;
    private int $totalItems;

    public function __construct(iterable $items, int $currentPage, int $itemsPerPage, int $totalItems)
    {
        $this->items = [...$items];
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalItems = $totalItems;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }


}
