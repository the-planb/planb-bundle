<?php

/**
 * This file is part of the planb project.
 *
 * (c) jmpantoja <jmpantoja@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PlanB\Framework\Event\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PlanB\Domain\Event\DomainEventInterface;
use PlanB\Domain\Event\Event;
use PlanB\Domain\Event\EventStore;
use Symfony\Component\Serializer\SerializerInterface;

final class DoctrineEventStore extends ServiceEntityRepository implements EventStore
{
    private SerializerInterface $serializer;

    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer)
    {
        parent::__construct($registry, Event::class);
        $this->serializer = $serializer;
    }

    public function persist(DomainEventInterface $domainEvent): void
    {
        $event = $this->serializer->serialize($domainEvent, 'json', ['groups' => 'read']);

        $event = new Event(...[
            get_class($domainEvent),
            $event,
            $domainEvent->when(),
        ]);


        $this->getEntityManager()->persist($event);
    }
}
