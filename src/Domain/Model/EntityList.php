<?php

namespace PlanB\Domain\Model;

use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Map\Map;

#[ElementType(Entity::class)]
final class EntityList extends Map
{
    public function normalizeKey(mixed $value, mixed $key): string
    {
        return (string)$value->getId();
    }
}
