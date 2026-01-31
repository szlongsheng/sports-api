<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Unit;
use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $account = trim($data['account'] ?? '');
        $password = $data['password'] ?? '';

        if (!$account || !$password) {
            $response->getBody()->write(json_encode(json_error(401, '请输入账号和密码')));
            return $response->withStatus(401);
        }

        $unit = Unit::where('account', $account)->first();
        if (!$unit || $unit->status !== 1) {
            $response->getBody()->write(json_encode(json_error(401, '账号或密码错误')));
            return $response->withStatus(401);
        }

        if (!password_verify($password, $unit->password)) {
            $response->getBody()->write(json_encode(json_error(401, '账号或密码错误')));
            return $response->withStatus(401);
        }

        $token = (new JwtService())->create([
            'id' => $unit->id,
            'account' => $unit->account,
            'guard' => 'unit',
        ]);
        $response->getBody()->write(json_encode(json_success([
            'token' => $token,
            'user' => ['id' => $unit->id, 'account' => $unit->account, 'name' => $unit->name],
        ])));
        return $response;
    }
}
