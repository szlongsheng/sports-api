<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProfileController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $request->getAttribute('user', []);
        $response->getBody()->write(json_encode(json_success([
            'profile' => $user,
            'message' => '单位信息',
        ])));
        return $response;
    }
}
