<?php

namespace PlanB\Tests\Framework\Api\Filter\Traits;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

trait FilterTrait
{
    use ProphecyTrait;

    private function giveMeATextFilter()
    {
        return new TextFilterBuilder($this->prophesize(...));
    }

    /**
     * @return QueryBuilder|\Prophecy\Prophecy\ObjectProphecy
     */
    private function giveMeAQueryBuilderThatAddWhere($code): QueryBuilder
    {
        $queryBuilder = $this->prophesize(QueryBuilder::class);

        $queryBuilder
            ->getRootAliases()
            ->willReturn(['A']);

        $queryBuilder
            ->andWhere($code)
            ->shouldBeCalledOnce();

        return $queryBuilder->reveal();
    }

    private function giveMeAQueryBuilderThatNeverChange(): QueryBuilder
    {
        $queryBuilder = $this->prophesize(QueryBuilder::class);

        $queryBuilder
            ->getRootAliases()
            ->willReturn(['A']);

        $queryBuilder
            ->andWhere(Argument::any())
            ->shouldNotBeCalled();

        return $queryBuilder->reveal();
    }

    private function giveMeANameGenerator(): QueryNameGeneratorInterface
    {
        return $this->prophesize(QueryNameGeneratorInterface::class)
            ->reveal();
    }
}