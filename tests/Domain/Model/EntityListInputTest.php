<?php

declare(strict_types=1);

namespace PlanB\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityId;
use PlanB\Domain\Model\EntityListInput;
use Prophecy\PhpUnit\ProphecyTrait;

final class EntityListInputTest extends TestCase
{
    use ProphecyTrait;


    public function test_it_calls_to_adder_properly()
    {
        $entityA = $this->give_me_an_entity('A');
        $entityB = $this->give_me_an_entity('B');

        $data = [
            $entityA,
            ['name' => 'pepe', 'age' => 19],
        ];

        $invoker = $this->prophesize(Invoker::class);
        $invoker->adder('pepe', 19)
            ->shouldBeCalled();

        $invoker = $invoker->reveal();

        $listInput = new EntityListInputExample($data);
        $listInput
            ->create($invoker->adder(...))
            ->with([
                $entityB,
            ]);
    }

    private function give_me_an_entity(string $id): Entity
    {
        $entityId = $this->prophesize(EntityId::class);
        $entityId->__toString()
            ->willReturn($id);

        $entity = $this->prophesize(Entity::class);
        $entity->getId()
            ->willReturn($entityId->reveal());

        return $entity->reveal();
    }

    public function test_it_calls_to_remover_properly()
    {
        $entityA = $this->give_me_an_entity('A');
        $entityB = $this->give_me_an_entity('B');

        $data = [
            $entityA,
            ['name' => 'pepe', 'age' => 19],
        ];

        $invoker = $this->prophesize(Invoker::class);
        $invoker->remover($entityB->getId())
            ->shouldBeCalled();

        $invoker = $invoker->reveal();

        $listInput = new EntityListInputExample($data);
        $listInput
            ->remove($invoker->remover(...))
            ->with([
                $entityB,
            ]);
    }

    public function test_it_calls_to_attacher_properly()
    {
        $entityA = $this->give_me_an_entity('A');
        $entityB = $this->give_me_an_entity('B');

        $data = [
            $entityA,
            ['name' => 'pepe', 'age' => 19],
        ];

        $invoker = $this->prophesize(Invoker::class);
        $invoker->attacher($entityA)
            ->shouldBeCalled();
        $invoker = $invoker->reveal();

        $listInput = new EntityListInputExample($data);
        $listInput
            ->add($invoker->attacher(...))
            ->with([
                $entityB,
            ]);
    }

}

class Invoker
{
    public function adder($name, $age)
    {
    }

    public function remover(EntityId $entity)
    {
    }

    public function attacher(Entity $entity)
    {
    }
}

class EntityListInputExample extends EntityListInput
{

}