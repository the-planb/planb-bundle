<?php

namespace PlanB\Framework\Api\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class FosUserContextHash extends AbstractController
{
    public function __invoke(): Response
    {
        if ('application/vnd.fos.user-context-hash' == strtolower($_SERVER['HTTP_ACCEPT'])) {
            $hash = $this->getHash();

            return new Response($hash, 200, [
                'X-User-Context-Hash' => $hash,
                'Content-Type' => 'application/vnd.fos.user-context-hash',
                'Cache-Control' => 'max-age=3600',
                'Vary' => 'Cookie, Authorization',
            ]);
        }

        return new Response(null, 406);
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        $user = $this->getUser();

        if (null === $user) {
            return md5(serialize([]));
        }

        $roles = $user->getRoles();
        sort($roles);
        return md5(serialize($roles));
    }
}
