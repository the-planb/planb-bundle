<?php

namespace PlanB\Tests\Framework\Api\Symfony\Controller;

use PHPUnit\Framework\TestCase;
use PlanB\Tests\Framework\Api\Symfony\Controller\Traits\ControllerTrait;

class FosUserContextHashTest extends TestCase
{
    use ControllerTrait;

    public function test_it_manages_an_invalid_request_properly()
    {
        $_SERVER['HTTP_ACCEPT'] = '';
        $controller = $this->giveMeAController()
            ->please();

        $response = $controller();
        $this->assertEquals(406, $response->getStatusCode());
    }

    public function test_it_manages_an_unauthorized_user_properly()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/vnd.fos.user-context-hash';
        $controller = $this->giveMeAController()
            ->please();

        $response = $controller();

        $hash = $response->headers->get('X-User-Context-Hash');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('40cd750bba9870f18aada2478b24840a', $hash);
    }

    public function test_it_manages_an_user_without_roles_properly()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/vnd.fos.user-context-hash';
        $controller = $this->giveMeAController()
            ->withAuthenticatedUser()
            ->please();

        $response = $controller();

        $hash = $response->headers->get('X-User-Context-Hash');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('40cd750bba9870f18aada2478b24840a', $hash);
    }

    public function test_it_manages_an_authorized_user_properly()
    {
        $_SERVER['HTTP_ACCEPT'] = 'application/vnd.fos.user-context-hash';
        $controller = $this->giveMeAController()
            ->withAuthenticatedUser([
                'ROLE_EDITOR',
                'ROLE_ADMIN',
            ])
            ->please();

        $response = $controller();

        $hash = $response->headers->get('X-User-Context-Hash');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('2a78c721cbe8b34548d33a7bd653fc67', $hash);
    }
}
