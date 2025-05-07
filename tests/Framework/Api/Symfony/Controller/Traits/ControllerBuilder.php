<?php

namespace PlanB\Tests\Framework\Api\Symfony\Controller\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use PlanB\Framework\Api\Symfony\Controller\FosUserContextHash;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\User\UserInterface;

final class ControllerBuilder
{
    private $prophesize;
    private MockObject $mock;
    private ?ObjectProphecy $user;

    public function __construct(callable $prophesize, MockObject $mock)
    {
        $this->prophesize = $prophesize;
        $this->mock = $mock;
        $this->user = null;
    }

    public function withAuthenticatedUser(array $roles = []): self
    {
        $this->user = ($this->prophesize)(UserInterface::class);
        $this->user->getRoles()
            ->willReturn($roles);

        return $this;
    }

    public function please(): FosUserContextHash|MockObject
    {
        $this->mock->method('getUser')
            ->willReturn($this->user?->reveal());

        return $this->mock;
    }


}
