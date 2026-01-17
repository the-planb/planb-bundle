<?php

declare(strict_types=1);

namespace PlanB\Framework\Api\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;

final class PostmanServer implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;
    private string $baseUrl;

    public function __construct(OpenApiFactoryInterface $decorated, string $baseUrl)
    {
        $this->decorated = $decorated;
        $this->baseUrl = !empty($baseUrl) ? $baseUrl : '/';
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $servers = [
            new Server($this->baseUrl, 'Postman'),
        ];

        return $openApi->withServers($servers);
    }
}
