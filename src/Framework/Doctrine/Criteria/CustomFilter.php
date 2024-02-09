<?php

namespace PlanB\Framework\Doctrine\Criteria;

use Doctrine\ORM\Query\Expr;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\Operator;

abstract class CustomFilter
{
    protected function iLike(Expr $expr, string $field, mixed $value): ?string
    {
        $field = $expr->lower($field);
        $value = strtolower($value);
        $value = "'$value'";

        return $expr->like($field, $value);
    }

    protected function iEq(Expr $expr, string $field, mixed $value): ?string
    {
        $field = $expr->lower($field);
        $value = strtolower($value);
        $value = "'$value'";

        return $expr->eq($field, $value);
    }

    public function apply(Expr $expr, Filter $filter, string $alias = null): ?string
    {
        $field = is_null($alias) ?
            $filter->getField() :
            "$alias.{$filter->getField()}";

        $operator = $filter->getOperator();
        $value = $filter->getValue();

        return match ($operator) {
            Operator::EQUALS => $this->eq($expr, $field, $value),
            Operator::NOT_EQUALS => $this->neq($expr, $field, $value),
            Operator::CONTAINS => $this->contains($expr, $field, $value),
            Operator::NOT_CONTAINS => $expr->not($this->contains($expr, $field, $value)),
            Operator::STARTS_WITH => $this->startsWith($expr, $field, $value),
            Operator::ENDS_WITH => $this->endsWith($expr, $field, $value),
            Operator::GREATER_THAN => $this->gt($expr, $field, $value),
            Operator::LESS_THAN => $this->lt($expr, $field, $value),
            Operator::GREATER_OR_EQUALS_THAN => $this->gte($expr, $field, $value),
            Operator::LESS_OR_EQUALS_THAN => $this->lte($expr, $field, $value),
            Operator::BETWEEN => $this->between($expr, $field, $value),
            Operator::IDENTITY => $this->identity($expr, $field, $value),
            Operator::NOT_IDENTITY => $expr->not($this->identity($expr, $field, $value)),
        };
    }

    abstract protected function eq(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function neq(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function contains(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function startsWith(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function endsWith(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function gt(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function lt(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function gte(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function lte(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function between(Expr $expr, string $field, mixed $value): ?string;

    abstract protected function identity(Expr $expr, string $field, mixed $value): ?string;
}
