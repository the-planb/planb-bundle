<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Event\Repository;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\Event;
use PlanB\Framework\Event\Repository\DoctrineEventStore;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Serializer\SerializerInterface;

final class DoctrineEventStoreTest extends TestCase
{
    use ProphecyTrait;

    public const EVENT_LIKE_JSON = 'event.like.a.json';

    public function test_it_persists_properly()
    {
        $date = new CarbonImmutable();
        $event = $this->give_me_a_event($date);

        $registry = $this->give_me_the_registry($date);
        $serializer = $this->give_me_the_serializer($event);

        $store = new DoctrineEventStore($registry, $serializer);

        $store->persist($event);
    }

    private function give_me_a_event(CarbonImmutable $date): DomainEventInterface
    {
        $event = $this->prophesize(DomainEventInterface::class);
        $event->when()->willReturn($date);

        return $event->reveal();
    }

    private function give_me_the_registry(CarbonImmutable $date): object
    {
        $metadata = $this->prophesize(ClassMetadata::class)->reveal();
        $metadata->name = 'XX';

        $manager = $this->prophesize(EntityManagerInterface::class);
        $manager->getClassMetadata(Event::class)
            ->willReturn($metadata);

        $registry = $this->prophesize(ManagerRegistry::class);
        $registry->getManagerForClass(Event::class)
            ->willReturn($manager->reveal());

        $manager->persist(
            Argument::that(function (Event $event) use ($date) {
                return self::EVENT_LIKE_JSON === $event->getEvent() && $event->getDate()->eq($date);
            })
        )->shouldBeCalled();

        return $registry->reveal();
    }

    private function give_me_the_serializer(DomainEventInterface $event): SerializerInterface
    {
        $serializer = $this->prophesize(SerializerInterface::class);
        $serializer->serialize($event, 'json', ['groups' => 'read'])->willReturn(self::EVENT_LIKE_JSON);

        return $serializer->reveal();
    }
}
