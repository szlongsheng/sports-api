<?php

declare(strict_types=1);

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class JwtAuthMiddleware implements MiddlewareInterface
{
    private string $guard;

    public function __construct(string $guard = 'api')
    {
        $this->guard = $guard;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        if (empty($header) || !preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $this->unauthorized('缺少认证信息');
        }

        $token = $matches[1];
        $secret = $_ENV['JWT_SECRET'] ?? 'default-secret';

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            $user = (array) $decoded->data;
            // 校验 guard：管理端 token 不能访问单位端接口，反之亦然
            if (($user['guard'] ?? '') !== $this->guard) {
                return $this->unauthorized('认证类型不匹配');
            }
            $request = $request->withAttribute('user', $user);
            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->unauthorized('认证无效或已过期');
        }
    }

    private function unauthorized(string $message): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode(json_error(401, $message)));
        return $response->withStatus(401);
    }
}
