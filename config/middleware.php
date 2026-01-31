<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonResponseMiddleware;

return function (App $app): void {
    // 解析 JSON 请求体
    $app->addBodyParsingMiddleware();

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
