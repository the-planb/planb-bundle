<?php

namespace PlanB\Tests\Framework\Doctrine\Fixtures;

use PHPUnit\Framework\TestCase;
use stdClass;

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

    public function testDenormalize()
    {

        $fixture = $this->giveMeAFixture()
            ->thatWillDenormalizeAnArray(stdClass::class, new stdClass(), 1)
            ->please();

        $response = $fixture->denormalize([], stdClass::class);

        $this->assertEquals($response, new stdClass());
    }

    public function testIriConverter()
    {

        $fixture = $this->giveMeAFixture()
            ->thatWillConvertResourceToIri(new stdClass(), '/esto/es/un/iri', 1)
            ->please();

        $response = $fixture->resourceToIri(new stdClass());

        $this->assertEquals($response, '/esto/es/un/iri');
    }

    public function testLoadData()
    {
        $command = new \stdClass();
        $fixture = $this->giveMeAFixture()
            ->thatWillCallHandle($command, 10)
            ->thatWillAddReferences(EntityExample::class, 10)
            ->please();

        $manager = $this->giveMeAManagerThatExecSql();
        $fixture->load($manager);
    }

    public function testGetManyReferences()
    {
        $fixture = $this->giveMeAFixture()
            ->thatHaveReferences(\stdClass::class, 10)
            ->please();

        $references = $fixture->getManyReferences(\stdClass::class, 2);
        $this->assertCount(2, $references);
        $this->assertContainsOnlyInstancesOf(\stdClass::class, $references);
    }

    public function testGetManyReferencesLikeIri()
    {
        $fixture = $this->giveMeAFixture()
            ->thatWillConvertResourceToIri(new stdClass(), '/esto/es/un/iri', 2)
            ->thatHaveReferences(\stdClass::class, 2)
            ->please();

        $references = $fixture->getManyReferencesLikeIri(\stdClass::class, 2);
        $this->assertCount(2, $references);
        $this->assertContainsOnly('string', $references);
    }

    public function testGetOneReference()
    {
        $fixture = $this->giveMeAFixture()
            ->thatHaveReferences(\stdClass::class, 10)
            ->please();

        $reference = $fixture->getOneReference(\stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $reference);
    }

    public function testGetOneReferenceLikeIri()
    {
        $fixture = $this->giveMeAFixture()
            ->thatWillConvertResourceToIri(new stdClass(), '/esto/es/un/iri', 1)
            ->thatHaveReferences(\stdClass::class, 10)
            ->please();

        $reference = $fixture->getOneReferenceLikeIri(\stdClass::class);
        $this->assertEquals('/esto/es/un/iri', $reference);
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
