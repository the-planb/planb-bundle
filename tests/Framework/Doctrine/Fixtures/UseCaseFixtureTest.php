<?php

namespace PlanB\Tests\Framework\Doctrine\Fixtures;

use PHPUnit\Framework\TestCase;

class UseCaseFixtureTest extends TestCase
{
    use FixtureTrait;

    public function testHandleACommand()
    {
        $command = new \stdClass();
        $fixture = $this->giveMeAFixture()
            ->thatWillCallHandle($command, 1)
            ->please();

        $fixture->handle($command);
    }

    public function testLoadData()
    {
        $command = new \stdClass();
        $fixture = $this->giveMeAFixture()
            ->thatWillCallHandle($command, 10)
            ->thatWillAddReferences(\stdClass::class, 10)
            ->please();

        $manager = $this->giveMeAManagerThatExecSql();
        $fixture->load($manager);
    }

    public function testGetSomeReferences()
    {
        $fixture = $this->giveMeAFixture()
            ->thatHaveReferences(\stdClass::class, 10)
            ->please();

        $references = $fixture->getSomeReferences(\stdClass::class, 2);
        $this->assertCount(2, $references);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $references);

    }

    public function testLoadDataWithInvalidEnv()
    {
        $fixture = $this->giveMeAFixture()
            ->withEnvironment('test')
            ->thatWillNeverHandle()
            ->please();

        $manager = $this->giveMeAManagerThatNeverExecSql();
        $fixture->load($manager);
    }
}
