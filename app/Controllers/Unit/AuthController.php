<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $account = $data['account'] ?? '';
        $password = $data['password'] ?? '';

        // TODO: 从数据库验证单位账号
        if ($account && $password) {
            $token = (new JwtService())->create(['id' => 1, 'account' => $account, 'guard' => 'unit']);
            $response->getBody()->write(json_encode(json_success(['token' => $token, 'user' => ['id' => 1, 'account' => $account]])));
            return $response;
        }

        $response->getBody()->write(json_encode(json_error(401, '账号或密码错误')));
        return $response->withStatus(401);
    }
}
