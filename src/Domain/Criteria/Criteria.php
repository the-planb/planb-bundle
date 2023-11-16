<?php

namespace PlanB\Domain\Criteria;

final class Criteria
{
    private FilterList $filters;
    private Order $order;
    private Pagination $pagination;

    public function __construct(FilterList $filters, Order $order, ?int $page, ?int $itemsPerPage)
    {
        $this->filters = $filters;
        $this->order = $order;
        $this->pagination = new Pagination($page, $itemsPerPage);
    }

    public static function fromValues(array $values): self
    {

        $page = null;
        $itemsPerPage = null;
        $filters = [];
        $order = Order::empty();

        foreach ($values as $name => $value) {
            match ($name) {
                'page' => $page = (int)$value,
                'itemsPerPage' => $itemsPerPage = (int)$value,
                'order' => $order = self::orderFromValue($value),
                default => $filters[] = self::filterFromValue($name, $value)
            };
        }

        return new self(FilterList::collect($filters), $order, $page, $itemsPerPage);
    }

    private static function filterFromValue(string $field, array $filter): Filter
    {
        $operator = array_key_first($filter);
        $value = $filter[$operator];

        return new Filter($field, Operator::from($operator), $value);
    }

    private static function orderFromValue(array $value): Order
    {
        $field = array_key_first($value);
        $type = $value[$field];

        return new Order($field, OrderDir::tryFrom($type));
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

}
