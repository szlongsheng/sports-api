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
        $group->get('/campaign', \App\Controllers\Common\PageController::class . ':unitCampaign');
        $group->get('/users', \App\Controllers\Common\PageController::class . ':unitUsers');
        $group->get('/tasks', \App\Controllers\Common\PageController::class . ':unitTasks');
        $group->get('/checkins', \App\Controllers\Common\PageController::class . ':unitCheckins');
        $group->get('/prizes', \App\Controllers\Common\PageController::class . ':unitPrizes');
        
        // ==================== 认证接口 ====================
        $group->post('/auth/login', \App\Controllers\Unit\AuthController::class . ':login');

        // ==================== API 路由（需要 JWT 认证）====================
        $group->group('/api', function (RouteCollectorProxy $inner) {
            // 单位信息
            $inner->get('/profile', \App\Controllers\Unit\ProfileController::class . ':index');
            $inner->put('/profile', \App\Controllers\Unit\ProfileController::class . ':update');

            // 活动管理（单位仅一个活动）
            $inner->get('/campaign', \App\Controllers\Unit\CampaignController::class . ':show');
            $inner->post('/campaign', \App\Controllers\Unit\CampaignController::class . ':save');

            // 用户管理
            $inner->get('/users', \App\Controllers\Unit\UnitUserController::class . ':index');
            $inner->get('/users/{id}', \App\Controllers\Unit\UnitUserController::class . ':show');
            $inner->put('/users/{id}', \App\Controllers\Unit\UnitUserController::class . ':update');

            // 打卡任务管理
            $inner->get('/tasks', \App\Controllers\Unit\UnitTaskController::class . ':index');
            $inner->get('/tasks/{id}', \App\Controllers\Unit\UnitTaskController::class . ':show');
            $inner->post('/tasks', \App\Controllers\Unit\UnitTaskController::class . ':store');
            $inner->put('/tasks/{id}', \App\Controllers\Unit\UnitTaskController::class . ':update');
            $inner->delete('/tasks/{id}', \App\Controllers\Unit\UnitTaskController::class . ':destroy');

            // 打卡记录审核
            $inner->get('/checkins', \App\Controllers\Unit\UnitCheckInController::class . ':index');
            $inner->get('/checkins/{id}', \App\Controllers\Unit\UnitCheckInController::class . ':show');
            $inner->post('/checkins/{id}/approve', \App\Controllers\Unit\UnitCheckInController::class . ':approve');
            $inner->post('/checkins/{id}/reject', \App\Controllers\Unit\UnitCheckInController::class . ':reject');

            // 奖品管理
            $inner->get('/prizes', \App\Controllers\Unit\UnitPrizeController::class . ':index');
            $inner->get('/prizes/{id}', \App\Controllers\Unit\UnitPrizeController::class . ':show');
            $inner->post('/prizes', \App\Controllers\Unit\UnitPrizeController::class . ':store');
            $inner->put('/prizes/{id}', \App\Controllers\Unit\UnitPrizeController::class . ':update');
            $inner->delete('/prizes/{id}', \App\Controllers\Unit\UnitPrizeController::class . ':destroy');

            // 文件上传
            $inner->post('/upload/image', \App\Controllers\Common\UploadController::class . ':image');
            $inner->post('/upload/file', \App\Controllers\Common\UploadController::class . ':file');
        })->add(new JwtAuthMiddleware('unit'));
    });
};
