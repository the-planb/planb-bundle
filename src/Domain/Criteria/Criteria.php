<?php

namespace PlanB\Domain\Criteria;

final class Criteria
{
    private FilterList $filters;
    private Order $order;
    private Pagination $pagination;
    private ?int $limit = null;

    public function __construct(FilterList $filters, Order $order, Pagination $pagination, ?int $limit = null)
    {
        $this->filters = $filters;
        $this->order = $order;
        $this->pagination = $pagination;
        $this->limit = $limit;
    }

    public static function empty(): self
    {
        return CriteriaBuilder::make()->build();
    }

    public static function fromArray(array $values): self
    {
        return CriteriaBuilder::make($values)->build();
    }

    public function getFilters(): FilterList
    {
        return $this->filters;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function getLimit(): int
    {
        return $this->limit ?? $this->pagination->getMaxResults();
    }


}
