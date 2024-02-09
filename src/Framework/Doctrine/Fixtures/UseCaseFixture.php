<?php

declare(strict_types=1);

namespace App\BookStore\Framework\Doctrine\Fixtures;

namespace PlanB\Framework\Doctrine\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use League\Tactician\CommandBus;
use PlanB\Domain\Model\Entity;
use PlanB\DS\Map\Map;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class UseCaseFixture extends Fixture implements ContainerAwareInterface
{
    protected Generator $faker;
    private CommandBus $commandBus;
    private ObjectManager $manager;
    private ContainerInterface $container;


    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->faker = Factory::create();

    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    final public function load(ObjectManager $manager): void
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');
        $env = $kernel->getEnvironment();


        $this->manager = $manager;
        if (!in_array($env, $this->allowedEnvironments())) {
            return;
        }

        $this->loadData();
    }

    abstract public function loadData(): void;

    abstract public function allowedEnvironments(): array;

    public function handle(object $command): mixed
    {
        return $this->commandBus->handle($command);
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

    public function getSomeReferences(string $className, int $min, int $max = null): array
    {
        $limit = is_null($max) ? $min : rand($min, $max);
        $references = $this->referenceRepository->getReferencesByClass()[$className];

        return Map::collect($references)
            ->shuffle()
            ->keys()
            ->take($limit)
            ->map(fn ($key) => $this->getReference($key))
            ->toArray();

    }

    public function getOneReference(string $className): mixed
    {
        $list = $this->getSomeReferences($className, 1);
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
