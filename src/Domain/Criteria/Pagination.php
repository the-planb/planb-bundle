<?php

namespace PlanB\Domain\Criteria;

final class Pagination
{
    private int $page;
    private int $itemsPerPage;

    public function __construct(?int $page = null, ?int $itemsPerPage = null)
    {
        $this->page = $page ?? 1;
        $this->itemsPerPage = $itemsPerPage ?? 10;
    }

    public function getFirstResult(): int
    {
        return ($this->page - 1) * $this->itemsPerPage;
    }

    public function getMaxResults(): int
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

}
