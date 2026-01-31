<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_success([
            'title'   => '管理端仪表盘',
            'stats'   => ['users' => 0, 'units' => 0],
            'message' => '欢迎使用管理端',
        ])));
        return $response;
    }
}
