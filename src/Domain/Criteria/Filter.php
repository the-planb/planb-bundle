<?php

namespace PlanB\Domain\Criteria;

final class Filter
{
    private string $field;
    private Operator $operator;
    private mixed $value;

    public function __construct(string $field, Operator $operation, mixed $value)
    {
        $this->field = $field;
        $this->operator = $operation;
        $this->value = $value;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }


}
