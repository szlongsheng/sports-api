<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PingController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_success(['time' => date('Y-m-d H:i:s')])));
        return $response;
    }
}
