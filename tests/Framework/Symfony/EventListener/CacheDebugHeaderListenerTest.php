<?php

namespace PlanB\Tests\Framework\Symfony\EventListener;

use PHPUnit\Framework\TestCase;
use PlanB\Framework\Symfony\EventListener\CacheDebugHeaderListener;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CacheDebugHeaderListenerTest extends TestCase
{
    use ProphecyTrait;

    public function test_it_add_header_to_response_properly()
    {
        $headers = new ResponseHeaderBag();
        $event = $this->giveMeAEventResponse(true, $headers);

        $listener = new CacheDebugHeaderListener();
        $listener->onKernelResponse($event);

        $this->assertTrue($headers->has("X-Cache-Debug"));
        $this->assertEquals("1", $headers->get("X-Cache-Debug"));
    }

    public function test_it_does_not_add_header_to_response_when_is_not_a_main_request()
    {
        $headers = new ResponseHeaderBag();
        $event = $this->giveMeAEventResponse(false, $headers);

        $listener = new CacheDebugHeaderListener();
        $listener->onKernelResponse($event);

        $this->assertFalse($headers->has("X-Cache-Debug"));
    }

    public function giveMeAEventResponse(bool $isMainRequest, ResponseHeaderBag $headers): ResponseEvent
    {
        $response = $this->prophesize(Response::class);
        $response->headers = $headers;

        $event = $this->prophesize(ResponseEvent::class);
        $event->isMainRequest()
            ->willReturn($isMainRequest);

        $event->getResponse()
            ->willReturn($response);

        return $event->reveal();
    }
}
