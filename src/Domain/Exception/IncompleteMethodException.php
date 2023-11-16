<?php

namespace PlanB\Domain\Exception;

use LogicException;
use Throwable;

final class IncompleteMethodException extends LogicException
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function custom(string $message): self
    {
        return new self($message);
    }

    public static function fromMethod(string $methodName): self
    {
        $message = "The method '$methodName' has not been implemented yet.";
        return new self($message);
    }
}
