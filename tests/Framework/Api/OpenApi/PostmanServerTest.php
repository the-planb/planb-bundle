<?php

declare(strict_types=1);

namespace PlanB\Tests\Framework\Api\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use PHPUnit\Framework\TestCase;
use PlanB\Framework\Api\OpenApi\PostmanServer;

final class PostmanServerTest extends TestCase
{
    public function testItAddsPostmanServer(): void
    {
        $decorated = $this->createMock(OpenApiFactoryInterface::class);
        $openApi = $this->createMock(OpenApi::class);
        $baseUrl = 'https://api.example.com';

        $decorated->expects($this->once())
            ->method('__invoke')
            ->with([])
            ->willReturn($openApi);

        $openApi->expects($this->once())
            ->method('withServers')
            ->with($this->callback(function (array $servers) use ($baseUrl) {
                if (count($servers) !== 1) {
                    return false;
                }
                $server = $servers[0];
                return $server instanceof Server && $server->getUrl() === $baseUrl && $server->getDescription() === 'Postman';
            }))
            ->willReturn($openApi);

        $postmanServer = new PostmanServer($decorated, $baseUrl);
        $result = $postmanServer->__invoke([]);

        $this->assertSame($openApi, $result);
    }

    public function testItAddsDefaultPostmanServerWhenBaseUrlIsEmpty(): void
    {
        $decorated = $this->createMock(OpenApiFactoryInterface::class);
        $openApi = $this->createMock(OpenApi::class);
        $baseUrl = '';

        $decorated->expects($this->once())
            ->method('__invoke')
            ->with([])
            ->willReturn($openApi);

        $openApi->expects($this->once())
            ->method('withServers')
            ->with($this->callback(function (array $servers) {
                if (count($servers) !== 1) {
                    return false;
                }
                $server = $servers[0];
                return $server instanceof Server && $server->getUrl() === '/' && $server->getDescription() === 'Postman';
            }))
            ->willReturn($openApi);

        $postmanServer = new PostmanServer($decorated, $baseUrl);
        $result = $postmanServer->__invoke([]);

        $this->assertSame($openApi, $result);
    }
}
