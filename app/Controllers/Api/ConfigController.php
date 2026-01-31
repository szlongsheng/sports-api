<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ConfigController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_success([
            'version'   => '1.0.0',
            'api_base'  => '/api',
            'features'  => [],
        ])));
        return $response;
    }
}
