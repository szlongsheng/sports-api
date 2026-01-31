<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Services\JwtService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function wxLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $code = $data['code'] ?? '';

        // TODO: 调用微信 code2session 获取 openid，创建/更新用户
        if ($code) {
            $token = (new JwtService())->create([
                'id'     => 1,
                'openid' => 'mock_openid_' . substr($code, 0, 8),
                'guard'  => 'api',
            ]);
            $response->getBody()->write(json_encode(json_success(['token' => $token])));
            return $response;
        }

        $response->getBody()->write(json_encode(json_error(400, '缺少 code 参数')));
        return $response->withStatus(400);
    }
}
