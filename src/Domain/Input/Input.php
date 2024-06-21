<?php

namespace PlanB\Domain\Input;

use Exception;
use PlanB\Domain\Model\EntityId;

abstract class Input
{
    /**
     * @throws Exception
     */
    final public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $className = static::class;
            if (!property_exists($className, $key)) {
                throw new Exception("El atributo {$key} no existe en '{$className}'");
            }

            $this->{$key} = $value;
        }
    }

    public function getId(): ?EntityId
    {
        return $this->id ?? null;
    }

    abstract public function toArray(): array;
}
