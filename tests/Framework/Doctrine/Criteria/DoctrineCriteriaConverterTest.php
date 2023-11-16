<?php

namespace PlanB\Tests\Framework\Doctrine\Criteria;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Criteria;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\FilterList;
use PlanB\Domain\Criteria\Operator;
use PlanB\Domain\Criteria\Order;
use PlanB\Domain\Criteria\OrderDir;
use PlanB\Framework\Doctrine\Criteria\DoctrineCriteriaConverter;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class DoctrineCriteriaConverterTest extends TestCase
{
    use ProphecyTrait;

    const QUERY_RESULT = 'query result';
    const QUERY_SCALAR_RESULT = 'query scalar result';

    private function give_me_a_builder(?string $where = null, ?array $order = null, ?int $firstResult = null, ?int $maxResults = null, bool $count = false): QueryBuilder
    {

        $expr = new Expr();

        $builder = $this->prophesize(QueryBuilder::class);
        $builder->expr()
            ->willReturn($expr);

        $query = $this->prophesize(Query::class);
        $query->execute()
            ->willReturn(self::QUERY_RESULT);

        $query->getSingleScalarResult()
            ->willReturn(self::QUERY_SCALAR_RESULT);

        $builder->getQuery()
            ->willReturn($query->reveal());

        if (is_string($where)) {
            $builder->where($where)
                ->shouldBeCalledOnce();
        } else {
            $builder->where(Argument::any())
                ->shouldNotBeCalled();
        }

        if (is_array($order)) {
            $builder->orderBy(...$order)
                ->shouldBeCalledOnce();
        } else {
            $builder->orderBy(Argument::class)
                ->shouldNotBeCalled();
        }

        if (is_int($firstResult)) {
            $builder->setFirstResult($firstResult)
                ->shouldBeCalledOnce();
        } else {
            $builder->setFirstResult(Argument::any())
                ->shouldNotBeCalled();
        }

        if (is_int($maxResults)) {
            $builder->setMaxResults($maxResults)
                ->shouldBeCalledOnce();
        } else {
            $builder->setMaxResults(Argument::any())
                ->shouldNotBeCalled();
        }

        if ($count) {
            $builder->select('count(A.id)')
                ->shouldBeCalled();
        } else {
            $builder->select(Argument::any())
                ->shouldNotBeCalled();
        }

        return $builder->reveal();
    }

    private function give_me_a_converter(QueryBuilder $builder): DoctrineCriteriaConverter
    {

        $repository = $this->prophesize(ServiceEntityRepository::class);
        $repository->createQueryBuilder(Argument::any())
            ->willReturn($builder);

        return new DoctrineCriteriaConverter($repository->reveal());

    }


    public function test_it_gets_a_doctrine_criteria_properly()
    {
        $criteria = new Criteria(
            filters: FilterList::collect([
                new Filter('title', Operator::EQUALS, 'the title'),
                new Filter('price', Operator::GREATER_THAN, 15),
            ]),
            order: new Order('price', OrderDir::ASC),
            page: 20,
            itemsPerPage: 10
        );


        $builder = $this->give_me_a_builder(
            where: "LOWER(A.title) = 'the title' AND A.price > 15",
            order: ['A.price', 'asc'],
            firstResult: 190,  //(20 - 1) * 10
            maxResults: 10
        );
        $converter = $this->give_me_a_converter($builder);

        $converter->match($criteria)
            ->getQuery();
    }

    public function test_it_gets_a_doctrine_criteria_without_order_properly()
    {
        $criteria = new Criteria(
            filters: FilterList::collect([
                new Filter('title', Operator::EQUALS, 'the title'),
                new Filter('price', Operator::GREATER_THAN, 15),
            ]),
            order: Order::empty(),
            page: 20,
            itemsPerPage: 10
        );

        $builder = $this->give_me_a_builder(
            where: "LOWER(A.title) = 'the title' AND A.price > 15",
            firstResult: 190,  //(20 - 1) * 10
            maxResults: 10
        );
        $converter = $this->give_me_a_converter($builder);

        $converter->match($criteria)
            ->getQuery();
    }

    public function test_it_gets_a_doctrine_criteria_without_filters_properly()
    {
        $criteria = new Criteria(
            filters: FilterList::collect(),
            order: new Order('price', OrderDir::ASC),
            page: 20,
            itemsPerPage: 10
        );


        $builder = $this->give_me_a_builder(
            order: ['A.price', 'asc'],
            firstResult: 190,  //(20 - 1) * 10
            maxResults: 10
        );
        $converter = $this->give_me_a_converter($builder);

        $converter->match($criteria)
            ->getQuery();
    }

    public function test_it_gets_a_query_builder_properly()
    {
        $builder = $this->give_me_a_builder();
        $converter = $this->give_me_a_converter($builder);

        $this->assertInstanceOf(QueryBuilder::class, $converter->getQueryBuilder());
    }


    public function test_it_gets_a_query_properly()
    {
        $builder = $this->give_me_a_builder();
        $converter = $this->give_me_a_converter($builder);

        $this->assertInstanceOf(Query::class, $converter->getQuery());
        $this->assertEquals(self::QUERY_RESULT, $converter->execute());
        $this->assertEquals(self::QUERY_SCALAR_RESULT, $converter->getSingleScalarResult());
    }


    public function test_it_gets_a_count_query_properly()
    {
        $criteria = new Criteria(
            filters: FilterList::collect([
                new Filter('title', Operator::EQUALS, 'the title'),
                new Filter('price', Operator::GREATER_THAN, 15),
            ]),
            order: Order::empty(),
            page: 20,
            itemsPerPage: 10
        );

        $builder = $this->give_me_a_builder(
            where: "LOWER(A.title) = 'the title' AND A.price > 15",
            count: true
//            firstResult: 190,  //(20 - 1) * 10
//            maxResults: 10
        );
        $converter = $this->give_me_a_converter($builder);

        $converter->count($criteria)
            ->getQuery();
    }
}
