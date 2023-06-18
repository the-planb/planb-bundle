<?php

declare(strict_types=1);

namespace PlanB\Domain\Model;

use PlanB\DS\Attribute\ElementType;
use PlanB\DS\Sequence\Sequence;

#[ElementType(Entity::class, 'array')]
abstract class EntityListInput extends Sequence
{
    private mixed $creator = null;
    private mixed $remover = null;
    private mixed $adder = null;

    public function create(callable $callback): self
    {
        $this->creator = $callback;

        return $this;
    }

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

    public function with(iterable $data): self
    {
        foreach ($this->getCandidatesForRemove($data) as $item) {
            ($this->remover)($item->getId());
        }

        foreach ($this->getCandidatesForAdd() as $item) {
            ($this->adder)($item);
        }

        foreach ($this->getCandidatesForCreate() as $item) {
            ($this->creator)(...$item);
        }

        return $this;
    }

    private function getCandidatesForRemove(iterable $data): iterable
    {
        if (!is_callable($this->remover)) {
            return [];
        }

        $current = Sequence::collect($data);
        $candidates = $this->filter(fn (mixed $item) => $item instanceof Entity);

        return $current->diff($candidates, function (Entity $first, Entity $second) {
            return strcmp((string)$first->getId(), (string)$second->getId());
        });
    }

    private function getCandidatesForAdd(): iterable
    {
        if (!is_callable($this->adder)) {
            return [];
        }

        return $this->filter(fn (mixed $item) => $item instanceof Entity);
    }

    private function getCandidatesForCreate(): iterable
    {
        if (!is_callable($this->creator)) {
            return [];
        }

        return $this->filter(fn (mixed $item) => is_array($item));
    }
}
