<?php

namespace PlanB\Tests\Framework\Doctrine\Fixtures;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

trait FixtureTrait
{
    use ProphecyTrait;

    private function giveMeAFixture(): FixtureBuilder
    {
        return new FixtureBuilder($this->prophesize(...));
    }

    private function giveMeAManagerThatExecSql(): EntityManager
    {
        $manager = $this->prophesize(EntityManager::class);
        $connection = $this->prophesize(Connection::class);

        $connection->exec(Argument::any())
            ->shouldBeCalledTimes(1);

        $manager->flush()
            ->shouldBeCalledTimes(1);

        $manager->getConnection()
            ->willReturn($connection->reveal());

        return $manager->reveal();
    }

    private function giveMeAManagerThatNeverExecSql(): EntityManager
    {
        $manager = $this->prophesize(EntityManager::class);
        $connection = $this->prophesize(Connection::class);

        $connection->exec(Argument::any())
            ->shouldNotBeCalled();

        $manager->flush()
            ->shouldNotBeCalled();

        $manager->getConnection()
            ->willReturn($connection->reveal());

        return $manager->reveal();
    }
}
