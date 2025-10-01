<?php

declare(strict_types=1);

namespace PlanB\Domain\Criteria;

final class CriteriaBuilder
{
    private FilterList $filters;
    private Order $order;
    private Pagination $pagination;
    private ?int $limit = null;

    public static function make(Criteria|array|null $initial = null): self
    {
        $builder = new self();

        if (is_array($initial)) {
            $builder->fromArray($initial);
        }

        if ($initial instanceof Criteria) {
            $builder->fromCriteria($initial);
        }

        return $builder;
    }

    public function __construct()
    {
        $this->filters = FilterList::collect();
        $this->order = Order::empty();
        $this->pagination = new Pagination();
    }

    public function fromArray(array $filters): self
    {
        ['order' => $order, 'page' => $page, 'itemsPerPage' => $itemsPerPage] = [
            'order' => [],
            'page' => 1,
            'itemsPerPage' => 10,
            ...$filters,
        ];
        unset($filters['order']);
        unset($filters['page']);
        unset($filters['itemsPerPage']);

        $this->withFilters($filters)
            ->withOrderList($order ?? [])
            ->withPagination((int)($page ?? 1), (int)($itemsPerPage ?? 10));

        return $this;
    }

    public function fromCriteria(Criteria $criteria): self
    {
        $this->filters = $criteria->getFilters();
        $this->order = $criteria->getOrder();
        $this->pagination = $criteria->getPagination();

        return $this;
    }

    public function withFilters(array $filters): self
    {
        $this->filters = FilterList::collect($filters, function ($value, $key) {
            return $value instanceof Filter ?
                $value :
                $this->filterFromValue($key, $value);
        });

        return $this;
    }

    public function addFilters(array $filters): self
    {
        $this->withFilters([
            ...$this->filters,
            ...$filters,
        ]);

        return $this;
    }

    private function withOrderList(array $orders): self
    {
        $list = map($orders, function ($value, $key) {
            if (!is_string($key) || !is_string($value)) {
                return null;
            }

            $type = strtolower($value);
            return new Order($key, OrderDir::tryFrom($type));
        });

        $this->order = $list->isEmpty() ?
            Order::empty() :
            $list->first();

        return $this;
    }

    public function withOrder(string $field, string|OrderDir $type): self
    {
        $type = $type instanceof OrderDir ?
            $type :
            OrderDir::tryFrom(strtolower($type));

        $this->order = new Order($field, $type);

        return $this;
    }

    public function withPagination(int $page, int $itemsPerPage): self
    {
        $this->pagination = new Pagination($page, $itemsPerPage);

        return $this;
    }

    public function withLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }


    public function build(): Criteria
    {
        return new Criteria($this->filters, $this->order, $this->pagination, $this->limit);
    }


    private function filterFromValue(string $field, array $filter): Filter
    {
        $operator = array_key_first($filter);
        $value = $filter[$operator];

        return new Filter($field, Operator::from($operator), $value);
    }


}
