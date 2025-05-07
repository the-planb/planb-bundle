<?php

namespace PlanB\Tests\Framework\Api\Symfony\Controller\Traits;

use PlanB\Framework\Api\Symfony\Controller\FosUserContextHash;
use Prophecy\PhpUnit\ProphecyTrait;

trait ControllerTrait
{
    use ProphecyTrait;

    private function giveMeAController()
    {
        $controller = $this->createPartialMock(FosUserContextHash::class, [
            'getUser',
        ]);

        return new ControllerBuilder($this->prophesize(...), $controller);
    }

}
