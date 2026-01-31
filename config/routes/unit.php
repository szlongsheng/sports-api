<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\JwtAuthMiddleware;

/**
 * 单位端路由配置
 * 路径前缀: /unit
 */
return function (App $app): void {
    $app->group('/unit', function (RouteCollectorProxy $group) {
        // ==================== 根路径智能跳转 ====================
        $group->get('', \App\Controllers\Common\RedirectController::class . ':unitRoot');
        $group->get('/', \App\Controllers\Common\RedirectController::class . ':unitRoot');
        
        // ==================== 页面路由 ====================
        $group->get('/login', \App\Controllers\Common\PageController::class . ':unitLogin');
        $group->get('/dashboard', \App\Controllers\Common\PageController::class . ':unitDashboard');
        $group->get('/profile', \App\Controllers\Common\PageController::class . ':unitProfile');
        
        // ==================== 认证接口 ====================
        $group->post('/auth/login', \App\Controllers\Unit\AuthController::class . ':login');

        // ==================== API 路由（需要 JWT 认证）====================
        $group->group('/api', function (RouteCollectorProxy $inner) {
            // 单位信息
            $inner->get('/profile', \App\Controllers\Unit\ProfileController::class . ':index');
            
            // 文件上传
            $inner->post('/upload/image', \App\Controllers\Common\UploadController::class . ':image');
            $inner->post('/upload/file', \App\Controllers\Common\UploadController::class . ':file');
        })->add(new JwtAuthMiddleware('unit'));
    });
};
