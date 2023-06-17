<?php

namespace PlanB\Tests\Framework\Api\Filter\Traits;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PlanB\Framework\Api\Filter\TextFilter;
use Prophecy\Argument;

final class TextFilterBuilder
{
    private $prophesize;

    private array $propertyNames = [];

    public function __construct(callable $prophesize)
    {
        $this->prophesize = $prophesize;
    }

    public function withPropertyNames(array $names): self
    {
        $this->propertyNames = $names;
        return $this;
    }


    public function please()
    {
        $metadata = ($this->prophesize)(ClassMetadata::class);
        $metadata->getFieldNames()
            ->willReturn($this->propertyNames);

        $metadata->hasField(Argument::in($this->propertyNames))
            ->willReturn(true);

        $objectManager = ($this->prophesize)(ObjectManager::class);
        $objectManager->getClassMetadata('resourceClass')
            ->willReturn($metadata->reveal());


        $registry = ($this->prophesize)(ManagerRegistry::class);
        $registry->getManagerForClass('resourceClass')
            ->willReturn($objectManager->reveal());

        return new TextFilter($registry->reveal(), null, array_flip($this->propertyNames));
    }
}