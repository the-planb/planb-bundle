<?php

namespace PlanB\Framework\Doctrine\Criteria;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PlanB\Domain\Criteria\Criteria;
use PlanB\Domain\Criteria\Filter;

final class DoctrineCriteriaConverter
{
    private CustomFilterFactory $filtersFactory;
    private ServiceEntityRepository $repository;

    private string $alias;
    private ?QueryBuilder $builder = null;

    final public function __construct(ServiceEntityRepository $repository, array $filters = [])
    {
        $this->repository = $repository;
        $this->alias = 'A';
        $this->filtersFactory = new CustomFilterFactory($filters);

    }

    public function match(Criteria $criteria): self
    {
        $this->reset()
            ->addFilters($criteria)
            ->addOrder($criteria)
            ->addFirstResult($criteria)
            ->addMaxResults($criteria);

        return $this;
    }

    private function reset(): self
    {
        $this->builder = $this->repository->createQueryBuilder($this->alias);
        return $this;
    }

    public function count(Criteria $criteria): self
    {
        $this->reset()
            ->select("count({$this->alias}.id)")
            ->addFilters($criteria);

        return $this;
    }

    private function select(string $select): self
    {
        $this->builder->select($select);
        return $this;
    }


    private function addFilters(Criteria $criteria): self
    {
        $filterList = $criteria->getFilters();

        if ($filterList->isEmpty()) {
            return $this;
        }

        $expr = $this->builder->expr();
        $expressions = $filterList->map(function (Filter $filter) use ($expr) {
            return $this->filtersFactory->applyFilter($expr, $filter, $this->alias);
        })->filter();

        $where = $expr->andX()
            ->addMultiple($expressions->toArray());

        $this->builder->where((string)$where);

        return $this;
    }


    private function addOrder(Criteria $criteria): self
    {
        $order = $criteria->getOrder();
        if ($order->isEmpty()) {
            return $this;
        }

        $field = "{$this->alias}.{$order->getField()}";
        $dir = $order->getType()->value;

        $this->builder->orderBy($field, $dir);
        return $this;
    }

    private function addFirstResult(Criteria $criteria): self
    {
        $pagination = $criteria->getPagination();
        $this->builder->setFirstResult($pagination->getFirstResult());
        return $this;
    }

    private function addMaxResults(Criteria $criteria): self
    {
        $pagination = $criteria->getPagination();
        $this->builder->setMaxResults($pagination->getMaxResults());
        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        if (is_null($this->builder)) {
            $this->reset();
        }

        return $this->builder;
    }

    public function getQuery(): Query
    {
        return $this->getQueryBuilder()
            ->getQuery();
    }

    public function execute(): mixed
    {
        return $this->getQuery()->execute();
    }

    public function getSingleScalarResult(): bool|float|int|null|string
    {
        return $this->getQuery()->getSingleScalarResult();
    }

}
