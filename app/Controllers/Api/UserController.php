<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    public function info(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user', []);
        $response->getBody()->write(json_encode(json_success([
            'userInfo' => array_merge($user, [
                'nickname' => '微信用户',
                'avatar'   => '',
            ]),
        ])));
        return $response;
    }
}
