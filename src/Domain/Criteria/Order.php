<?php

namespace PlanB\Domain\Criteria;

final class Order
{
    private ?string $field;
    private ?OrderDir $type;

    public function __construct(string $field = null, OrderDir $type = null)
    {
        $this->field = $field;
        $this->type = $type;

    }

    public static function empty(): self
    {
        return new Order();
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getType(): OrderDir
    {
        return $this->type;
    }

    public function isEmpty(): bool
    {
        return is_null($this->field) || is_null($this->type);
    }

}
