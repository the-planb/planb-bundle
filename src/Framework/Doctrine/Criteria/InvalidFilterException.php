<?php

namespace PlanB\Framework\Doctrine\Criteria;

use PlanB\Domain\Criteria\Operator;
use RuntimeException;
use Throwable;

final class InvalidFilterException extends RuntimeException
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function make(Operator $operator, string $field): self
    {
        return new self("Filter of type '{$operator->value}' is not applicable to field '$field'");
    }
}
