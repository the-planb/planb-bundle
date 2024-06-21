<?php

declare(strict_types=1);

namespace PlanB\Domain\Input;

use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityList;
use PlanB\DS\Map\Map;

abstract class InputList extends Map
{
    private mixed $remover = null;
    private mixed $adder = null;

    public function remove(callable $callback): self
    {
        $this->remover = $callback;
        return $this;
    }


    public function add(callable $callback): self
    {
        $this->adder = $callback;
        return $this;
    }

    public function with(iterable $data): array
    {
        $data = EntityList::collect($data);
        is_callable($this->remover) && $this->forDeletion($data)
            ->each($this->remover);

        $this->each(function (Entity|array $item) use ($data) {
            $this->withItem($item, $data);

        });

        return [];
    }

    private function forDeletion(EntityList $data): EntityList
    {
        $keys = $this
            ->filter(fn (Entity|array $item) => $item instanceof Entity)
            ->mapKeys(function (Entity $item) {
                return (string)($item->getId());
            })
            ->filter();

        return $data->diffKeys($keys);
    }

    private function withItem(Entity|array $item, EntityList $data): void
    {
        if ($item instanceof Entity) {
            $id = $item->getId();

            !$data->hasKey((string)$id) && is_callable($this->adder) && ($this->adder)($item);
            return;
        }

        is_callable($this->adder) && ($this->adder)(...$item);
    }
}
