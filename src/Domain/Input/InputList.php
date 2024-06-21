<?php

declare(strict_types=1);

namespace PlanB\Domain\Input;

use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityList;
use PlanB\DS\Map\Map;

abstract class InputList extends Map
{
    private mixed $remover = null;
    private mixed $creator = null;
    //    private mixed $updater = null;
    private mixed $adder = null;

    public function remove(callable $callback): self
    {
        $this->remover = $callback;

        return $this;
    }

    public function create(callable $callback): self
    {
        $this->creator = $callback;

        return $this;
    }

    public function add(callable $callback): self
    {
        $this->adder = $callback;

        return $this;
    }

    public function with(EntityList $data): array
    {
        is_callable($this->remover) && $this->forDeletion($data)
            ->each($this->remover);

        $this->each(function (Entity|Input $item) use ($data) {
            $this->withItem($item, $data);

        });

        return [];
    }

    private function forDeletion(EntityList $data): EntityList
    {
        $keys = $this
            ->mapKeys(function (Entity|Input $item) {
                return (string)($item->getId());
            })
            ->filter();

        return $data->diffKeys($keys);
    }

    private function withItem(Entity|Input $item, EntityList $data): void
    {
        $id = $item->getId();

        if ($item instanceof Entity) {
            !$data->hasKey((string)$id) && is_callable($this->adder) && ($this->adder)($item);
            return;
        }

        $input = (array)$item;
        unset($input['id']);

        if (is_null($id)) {
            is_callable($this->creator) && ($this->creator)(...$input);
            return;
        }

        //        $entity = $data->get((string)$id, null);
        //
        //        if (is_null($entity)) return;
        //        $entity->update(...$input);
    }


}
