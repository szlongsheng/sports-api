<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Admin;
use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function loginPage(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_success([
            'message' => '管理端登录页面，请使用 POST /admin/auth/login 登录',
        ])));
        return $response;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (!$username || !$password) {
            $response->getBody()->write(json_encode(json_error(401, '请输入用户名和密码')));
            return $response->withStatus(401);
        }

        $admin = Admin::where('username', $username)->first();
        if (!$admin || $admin->status !== 1) {
            $response->getBody()->write(json_encode(json_error(401, '用户名或密码错误')));
            return $response->withStatus(401);
        }

        if (!password_verify($password, $admin->password)) {
            $response->getBody()->write(json_encode(json_error(401, '用户名或密码错误')));
            return $response->withStatus(401);
        }

        $token = (new JwtService())->create([
            'id' => $admin->id,
            'username' => $admin->username,
            'guard' => 'admin',
        ]);
        $response->getBody()->write(json_encode(json_success([
            'token' => $token,
            'user' => ['id' => $admin->id, 'username' => $admin->username, 'name' => $admin->name],
        ])));
        return $response;
    }
}
