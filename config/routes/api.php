<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\JwtAuthMiddleware;

/**
 * 小程序 API 路由配置
 * 路径前缀: /api
 */
return function (App $app): void {
    $app->group('/api', function (RouteCollectorProxy $group) {
        // ==================== 公开接口 ====================
        // 微信小程序登录
        $group->post('/auth/wxlogin', \App\Controllers\Api\AuthController::class . ':wxLogin');
        
        // 系统配置
        $group->get('/config', \App\Controllers\Api\ConfigController::class . ':index');

        // ==================== 需要登录的接口（JWT 认证）====================
        $group->group('', function (RouteCollectorProxy $inner) {
            // 用户信息
            $inner->get('/user/info', \App\Controllers\Api\UserController::class . ':info');
            
            // TODO: 在这里添加更多需要认证的小程序接口
            // 例如：
            // $inner->get('/user/orders', \App\Controllers\Api\OrderController::class . ':index');
            // $inner->post('/user/orders', \App\Controllers\Api\OrderController::class . ':create');
        })->add(new JwtAuthMiddleware('api'));
    });
};
