<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

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
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // TODO: 从数据库验证，使用 password_verify($password, $admin->password)
        if ($username === 'admin' && $password === 'admin123') {
            $token = (new JwtService())->create(['id' => 1, 'username' => $username, 'guard' => 'admin']);
            $response->getBody()->write(json_encode(json_success(['token' => $token, 'user' => ['id' => 1, 'username' => $username, 'name' => '管理员']])));
            return $response;
        }

        $response->getBody()->write(json_encode(json_error(401, '用户名或密码错误')));
        return $response->withStatus(401);
    }
}
