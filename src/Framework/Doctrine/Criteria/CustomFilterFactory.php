<?php

namespace PlanB\Framework\Doctrine\Criteria;

use Doctrine\ORM\Query\Expr;
use PlanB\Domain\Criteria\Filter;
use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Map\Map;

#[ElementType(CustomFilter::class)]
final class CustomFilterFactory extends Map
{
    public function applyFilter(Expr $expr, Filter $filter, string $alias): ?string
    {
        $custom = $this->findFilter($filter);

        return $custom->apply($expr, $filter, $alias);
    }

    private function findFilter(Filter $filter): CustomFilter
    {
        return $this->get($filter->getField(), new DefaultFilter());
    }
}
