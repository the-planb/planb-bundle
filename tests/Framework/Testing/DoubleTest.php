<?php

namespace PlanB\Tests\Framework\Testing;

use PHPUnit\Framework\TestCase;
use PlanB\DS\Sequence\Sequence;
use PlanB\Framework\Testing\Double;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class DoubleTest extends TestCase
{
    use ProphecyTrait;

    public function testIt()
    {
        $builder = new DoubleBuilder($this->prophesize(...), function (DoubleBuilder $builder) {
            $builder->withCount(111);
        });

        $sequence = $builder->reveal();
        $this->assertEquals(111, $sequence->count());
        $this->assertEquals(111, $sequence->filter()->count());


    }
}

class DoubleBuilder extends Double
{

    public function reveal(): Sequence
    {
        return $this->double()
            ->reveal();
    }

    public function withCount(int $length): self
    {
        $this->double()
            ->count()
            ->willReturn($length);

        return $this;
    }

    protected function classNameOrInterface(): string
    {
        return Sequence::class;
    }

    protected function double(): ObjectProphecy|\DateTime
    {
        return $this->double;
    }


}