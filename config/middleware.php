<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonResponseMiddleware;

return function (App $app): void {
    // 解析 JSON 请求体（跳过 multipart，避免影响文件上传）
    $app->add(new class implements \Psr\Http\Server\MiddlewareInterface {
        public function process(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
        {
            $ct = $request->getHeader('Content-Type')[0] ?? '';
            if (stripos($ct, 'multipart/form-data') === 0) {
                return $handler->handle($request);
            }
            return (new \Slim\Middleware\BodyParsingMiddleware())->process($request, $handler);
        }
    });

    // 路由中间件
    $app->addRoutingMiddleware();

    // 跨域（API 端必需）
    $app->add(CorsMiddleware::class);

    // 统一 JSON 响应格式
    $app->add(JsonResponseMiddleware::class);

    // 错误处理
    $errorMiddleware = $app->addErrorMiddleware(
        ($_ENV['APP_DEBUG'] ?? false) === 'true',
        true,
        true
    );
    $errorMiddleware->setDefaultErrorHandler(new \App\Handlers\ErrorHandler());
};
