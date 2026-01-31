<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_success([
            'title'   => '单位端仪表盘',
            'message' => '欢迎使用单位端',
        ])));
        return $response;
    }
}
