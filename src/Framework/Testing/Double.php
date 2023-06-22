<?php

namespace PlanB\Framework\Testing;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

abstract class Double
{
    protected readonly ObjectProphecy $double;
    /**
     * @var callable
     */
    private $prophesize;

    /**
     * @throws \ReflectionException
     */
    public function __construct(callable $prophesize, callable $configurator = null)
    {
        $classNameOrInterface = $this->classNameOrInterface();
        $this->prophesize = $prophesize;

        $this->double = $this->mock($classNameOrInterface);
        $this->configure();
        is_callable($configurator) && $configurator($this);
    }

    protected function configure(): void
    {
    }

    abstract protected function classNameOrInterface(): string;

    abstract public function reveal(): object;

    /**
     * @throws \ReflectionException
     */
    protected function mock(string $classNameOrInterface): ObjectProphecy
    {
        $mock = ($this->prophesize)($classNameOrInterface);
        $this->addFluency($mock, $classNameOrInterface);

        return $mock;
    }

    /**
     * @throws \ReflectionException
     */
    private function addFluency(ObjectProphecy $mock, string $classNameOrInterface): void
    {
        $class = new \ReflectionClass($classNameOrInterface);

        $methods = array_filter($class->getMethods(), function (\ReflectionMethod $method) {
            $returnType = $method->getReturnType();
            if (!$returnType instanceof \ReflectionNamedType) {
                return false;
            }

            return in_array($returnType->getName(), ['self', 'static']);
        });

        foreach ($methods as $method) {
            $mock
                ->{$method->getName()}(Argument::cetera())
                ->willReturn($mock);
        }
    }

    abstract protected function double(): mixed;
}
