<?php

namespace PlanB\Framework\Doctrine\Criteria;

use Doctrine\ORM\Query\Expr;

class DefaultFilter extends CustomFilter
{
    public function eq(Expr $expr, string $field, mixed $value): ?string
    {
        return $this->iEq($expr, $field, $value);
    }

    public function neq(Expr $expr, string $field, mixed $value): ?string
    {
        return $expr->not($this->iEq($expr, $field, $value));
    }

    public function contains(Expr $expr, string $field, mixed $value): ?string
    {
        return $this->iLike($expr, $field, "%$value%");
    }

    public function startsWith(Expr $expr, string $field, mixed $value): ?string
    {
        return $this->iLike($expr, $field, "$value%");
    }

    public function endsWith(Expr $expr, string $field, mixed $value): ?string
    {
        return $this->iLike($expr, $field, "%$value");
    }

    public function gt(Expr $expr, string $field, mixed $value): ?string
    {
        return $expr->gt($field, $value);
    }

    public function lt(Expr $expr, string $field, mixed $value): ?string
    {
        return $expr->lt($field, $value);
    }

    public function gte(Expr $expr, string $field, mixed $value): ?string
    {
        return $expr->gte($field, $value);
    }

    public function lte(Expr $expr, string $field, mixed $value): ?string
    {
        return $expr->lte($field, $value);
    }

    public function between(Expr $expr, string $field, mixed $value): ?string
    {
        [$min, $max] = explode('..', $value);
        return $expr->between($field, $min, $max);
    }

    public function identity(Expr $expr, string $field, mixed $value): ?string
    {
        $value = "'$value'";
        return $expr->eq($field, $value);
    }


}
