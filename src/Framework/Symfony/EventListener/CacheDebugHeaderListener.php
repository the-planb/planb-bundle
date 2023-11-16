<?php

namespace PlanB\Framework\Symfony\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class CacheDebugHeaderListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {

        if (!$event->isMainRequest()) {
            // don't do anything if it's not the master request
            return;
        }


        $response = $event->getResponse();
        $response->headers->set("X-Cache-Debug", '1');
    }
}
