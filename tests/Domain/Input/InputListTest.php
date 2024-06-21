<?php
declare(strict_types=1);

namespace PlanB\Tests\Domain\Input;

use PHPUnit\Framework\TestCase;
use PlanB\Domain\Input\InputList;
use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityId;
use PlanB\Domain\Model\EntityList;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class InputListTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_do_nothing_because_data_does_not_changed()
    {
        $client = $this->configureClient(function (ObjectProphecy $mock) {
            $mock->add(Argument::cetera())->shouldBeCalledTimes(0);
            $mock->remove(Argument::cetera())->shouldBeCalledTimes(0);
        });

        $data = [
            $this->createEntity(),
            $this->createEntity(),
            $this->createEntity()
        ];

        $this->setUpInputList($data, $data, $client);
    }

    public function test_it_add_new_elements_on_a_previous_dataset()
    {
        $client = $this->configureClient(function (ObjectProphecy $mock) {
            $mock->add(Argument::cetera())->shouldBeCalledTimes(2);
            $mock->remove(Argument::cetera())->shouldBeCalledTimes(0);
        });

        $entityA = $this->createEntity();
        $entityB = $this->createEntity();
        $entityC = $this->createEntity();

        $this->setUpInputList([
            $entityA
        ], [
            $entityA,
            $entityB,
            $entityC
        ], $client);
    }

    public function test_it_create_and_add_new_elements_on_a_previous_dataset()
    {
        $client = $this->configureClient(function (ObjectProphecy $mock) {
            $mock->add(Argument::cetera())->shouldBeCalledTimes(2);
            $mock->remove(Argument::cetera())->shouldBeCalledTimes(0);
        });

        $entityA = $this->createEntity();
        $entityB = $this->createInputArray();;
        $entityC = $this->createInputArray();;


        $this->setUpInputList([
            $entityA
        ], [
            $entityA,
            $entityB,
            $entityC,
        ], $client);
    }

    public function test_it_removes_the_leftovers_elements()
    {
        $client = $this->configureClient(function (ObjectProphecy $mock) {
            $mock->add(Argument::cetera())->shouldBeCalledTimes(0);
            $mock->remove(Argument::cetera())->shouldBeCalledTimes(2);
        });

        $entityA = $this->createEntity();
        $entityB = $this->createEntity();
        $entityC = $this->createEntity();

        $this->setUpInputList([
            $entityA,
            $entityB,
            $entityC,
        ], [
            $entityA,
        ], $client);
    }

    public function test_it_all_together_now()
    {
        $client = $this->configureClient(function (ObjectProphecy $mock) {
            $mock->add(Argument::cetera())->shouldBeCalledTimes(2);
            $mock->remove(Argument::cetera())->shouldBeCalledTimes(1);
        });

        $entityA = $this->createEntity();
        $entityB = $this->createEntity();
        $entityC = $this->createInputArray();

        $this->setUpInputList([
            $entityA,
        ], [
            $entityB,
            $entityC
        ], $client);
    }

    private function setUpInputList(array $current, array $input, InputListClientInterface $client)
    {
        $inputList = new class($input) extends InputList {

        };

        $entityList = EntityList::collect($current);
        $inputList
            ->add($client->add(...))
            ->remove($client->remove(...))
            ->with($entityList);

    }

    private function createEntity(): Entity
    {
        return new class implements Entity {
            private EntityId $id;

            public function __construct()
            {
                $this->id = new class extends EntityId {
                };
            }

            public function getId(): EntityId
            {
                return $this->id;
            }
        };
    }

    private function createInputArray(): array
    {
        return [];
    }

    private function configureClient(callable $callback)
    {
        $mock = $this->prophesize(InputListClientInterface::class);
        $callback($mock);
        return $mock->reveal();
    }

    private function add()
    {
        dump('xxxx');
        return $this;
    }

}

interface InputListClientInterface
{
    public function add();

    public function remove();

}
