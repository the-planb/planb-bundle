<?php

namespace PlanB\Tests\Framework\Doctrine\Fixtures;

use ApiPlatform\Metadata\IriConverterInterface;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use League\Tactician\CommandBus;
use PlanB\Domain\Model\Entity;
use PlanB\Domain\Model\EntityId;
use PlanB\Framework\Doctrine\Fixtures\UseCaseFixture;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class FixtureBuilder
{

    private CommandBus|ObjectProphecy $commandBus;
    private DenormalizerInterface|ObjectProphecy $denormalizer;
    private ReferenceRepository|ObjectProphecy $referenceRepository;
    private IriConverterInterface|ObjectProphecy $iriConverter;

    private $prophesize;


    public function __construct(callable $prophesize)
    {
        $this->prophesize = $prophesize;

        $this->commandBus = ($this->prophesize)(CommandBus::class);
        $this->denormalizer = ($this->prophesize)(DenormalizerInterface::class);
        $this->iriConverter = ($this->prophesize)(IriConverterInterface::class);

        $this->referenceRepository = ($this->prophesize)(ReferenceRepository::class);
    }

    public function thatWillCallHandle(object $command, int $times): self
    {
        $this->commandBus
            ->handle($command)
            ->shouldBeCalledTimes($times);

        return $this;
    }

    public function thatWillDenormalizeAnArray(string $type, object $response, int $times): self
    {
        $this->denormalizer
            ->denormalize(Argument::type('array'), $type, Argument::cetera())
            ->willReturn($response)
            ->shouldBeCalledTimes($times);

        return $this;
    }

    public function thatWillConvertResourceToIri(object $input, string $response, int $times): self
    {
        $this->iriConverter
            ->getIriFromResource(Argument::type(type_of($input)), Argument::cetera())
            ->willReturn($response)
            ->shouldBeCalledTimes($times);

        return $this;
    }


    public function thatWillNeverHandle(): self
    {
        $this->commandBus
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        return $this;
    }

    public function thatWillAddReferences(string $type, int $times): self
    {
        $this->referenceRepository
            ->addReference(Argument::containingString($type), Argument::type($type))
            ->shouldBeCalledTimes($times);


        $references = [$type => []];
        foreach (range(0, $times) as $key) {
            $references["{$type}_{$key}"] = ($this->prophesize)(\stdClass::class)->reveal();
        }

        $this->referenceRepository
            ->getReferencesByClass()
            ->willReturn($references);

        return $this;
    }

    public function thatHaveReferences(string $type, int $count): self
    {
        $references = [$type => []];
        foreach (range(0, $count) as $key) {
            $references[$type]["{$type}_{$key}"] = ($this->prophesize)(\stdClass::class)->reveal();
        }

        $this->referenceRepository
            ->getReferencesByClass()
            ->willReturn($references);

        $keys = array_keys($references[$type]);
        $this->referenceRepository
            ->getReference(Argument::in($keys), Argument::cetera())
            ->willReturn(($this->prophesize)(\stdClass::class)->reveal());

        return $this;
    }


    public function withEnvironment(string $env): self
    {
        $this->env = $env;
        return $this;
    }


    public function please(): UseCaseFixture
    {

        $fixture = new FixtureExample($this->commandBus->reveal(), $this->denormalizer->reveal(), $this->iriConverter->reveal());
        $fixture->setReferenceRepository($this->referenceRepository->reveal());

        return $fixture;
    }
}

class EntityExample implements Entity
{

    public function getId(): EntityId
    {
        $id = new class extends EntityId {
        };
        return $id;
    }
}

class FixtureExample extends UseCaseFixture
{

    public function loadData(): void
    {
        $this->createMany(10, function () {
            $this->handle(new stdClass());

            return new EntityExample();
        });

        $this->loadSqlFile(__DIR__ . '/data/data.sql');
    }

    public function loadRandomData(): void
    {
        $this->createRandomRange(10, function () {
            $this->handle(new \stdClass());

            return new EntityExample();
        });
    }

    public function allowedEnvironments(): array
    {
        return ['dev'];
    }
}
