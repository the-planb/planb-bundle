<?php

declare(strict_types=1);

namespace App\BookStore\Framework\Doctrine\Fixtures;

namespace PlanB\Framework\Doctrine\Fixtures;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use League\Tactician\CommandBus;
use PlanB\Domain\Model\Entity;
use PlanB\DS\Map\Map;
use PlanB\DS\Vector\Vector;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

abstract class UseCaseFixture extends Fixture
{
    protected Generator $faker;
    private CommandBus $commandBus;
    private ObjectManager $manager;
    private DenormalizerInterface $denormalizer;
    private IriConverterInterface $iriConverter;


    public function __construct(CommandBus $commandBus, DenormalizerInterface $denormalizer, IriConverterInterface $iriConverter)
    {
        $this->faker = Factory::create();
        $this->commandBus = $commandBus;
        $this->denormalizer = $denormalizer;
        $this->iriConverter = $iriConverter;
    }

    final public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->loadData();
    }

    abstract public function loadData(): void;

    public function handle(object $command): mixed
    {
        return $this->commandBus->handle($command);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function resourceToIri(object|string $resource, int $referenceType = UrlGeneratorInterface::ABS_PATH, ?Operation $operation = null, array $context = []): mixed
    {
        return $this->iriConverter->getIriFromResource($resource, $referenceType, $operation, $context);
    }

    protected function createMany(int $count, callable $callback): array
    {
        $range = range(1, $count);

        return $this->createRange($range, $callback);
    }

    protected function createRange(array $range, callable $callback): array
    {
        $items = [];
        foreach ($range as $key => $value) {
            $entity = $callback($value, $key);

            if ($entity instanceof Entity) {
                $this->addReference($this->referenceName($entity::class, $key), $entity);
            }
            if (!is_null($entity)) {
                $items[] = $entity;
            }
        }

        return $items;
    }


    protected function referenceName(string $className, int $key): string
    {
        return "{$className}_{$key}";
    }

    public function getReferencesList(string $className, int $min, int $max = null): Vector
    {
        $limit = is_null($max) ? $min : rand($min, $max);
        $references = $this->referenceRepository->getReferencesByClass()[$className];

        return Map::collect($references)
            ->shuffle()
            ->keys()
            ->take($limit)
            ->map(fn ($key) => $this->getReference($key, $className));
    }

    public function getManyReferences(string $className, int $min, int $max = null): array
    {
        return $this->getReferencesList($className, $min, $max)
            ->toArray();
    }

    public function getManyReferencesLikeIri(string $className, int $min, int $max = null): array
    {
        return $this->getReferencesList($className, $min, $max)
            ->map($this->resourceToIri(...))
            ->toArray();
    }

    public function getOneReference(string $className): mixed
    {
        $list = $this->getManyReferences($className, 1);
        return array_pop($list);
    }

    public function getOneReferenceLikeIri(string $className): string
    {
        $list = $this->getManyReferencesLikeIri($className, 1);
        return array_pop($list);
    }

    protected function loadSqlFile(string $filepath): void
    {
        $sql = file_get_contents($filepath);

        /** @phpstan-ignore-next-line */
        $this->manager->getConnection()->exec($sql);
        $this->manager->flush();
    }

}
