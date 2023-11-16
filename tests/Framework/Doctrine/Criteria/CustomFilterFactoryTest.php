<?php

namespace PlanB\Tests\Framework\Doctrine\Criteria;

use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\Query\Expr;
use PHPUnit\Framework\TestCase;
use PlanB\Domain\Criteria\Filter;
use PlanB\Domain\Criteria\Operator;
use PlanB\Framework\Doctrine\Criteria\CustomFilter;
use PlanB\Framework\Doctrine\Criteria\CustomFilterFactory;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class CustomFilterFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_applies_a_filter_properly()
    {
        $filter = new Filter('name', Operator::CONTAINS, 'value');
        $expr = new Expr();
        $customFilter = $this->give_me_a_custom_filter_that_call_is_applied_once($expr, $filter, 'A');

        $factory = CustomFilterFactory::collect([
            'name' => $customFilter,
        ]);

        $sentence = $factory->applyFilter($expr, $filter, 'A');
        $this->assertEquals('sentence', $sentence);
    }

    public function test_it_applies_the_default_filter_properly()
    {
        $filter = new Filter('name', Operator::CONTAINS, 'value');
        $expr = new Expr();
        $customFilter = $this->give_me_a_custom_filter_that_call_is_never_applied();

        $factory = CustomFilterFactory::collect([
            'XXXX' => $customFilter,
        ]);

        $sentence = $factory->applyFilter($expr, $filter, 'A');
        $this->assertEquals("LOWER(A.name) LIKE '%value%'", $sentence);
    }

    private function give_me_a_custom_filter_that_call_is_applied_once(
        Expr   $expr,
        Filter $filter,
        string $alias
    ): CustomFilter
    {

        $customFilter = $this->prophesize(CustomFilter::class);

        $customFilter->apply($expr, $filter, $alias)
            ->shouldBeCalledOnce()
            ->willReturn('sentence');

        return $customFilter->reveal();
    }

    private function give_me_a_custom_filter_that_call_is_never_applied(): CustomFilter
    {
        $expression = $this->prophesize(Expression::class)->reveal();
        $customFilter = $this->prophesize(CustomFilter::class);

        $customFilter->apply(Argument::cetera())
            ->shouldNotBeCalled()
            ->willReturn($expression);

        return $customFilter->reveal();
    }

    private function give_me_an_expression_builder(): ExpressionBuilder
    {
        $expression = $this->prophesize(Expression::class)->reveal();
        $builder = $this->prophesize(ExpressionBuilder::class);

        $builder->contains(Argument::cetera())
            ->willReturn($expression);

        return $builder->reveal();
    }
}
