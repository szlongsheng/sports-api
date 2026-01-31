<?php

declare(strict_types=1);

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\JwtAuthMiddleware;

/**
 * 管理端路由配置
 * 路径前缀: /admin
 */
return function (App $app): void {
    $app->group('/admin', function (RouteCollectorProxy $group) {
        // ==================== 根路径智能跳转 ====================
        $group->get('', \App\Controllers\Common\RedirectController::class . ':adminRoot');
        $group->get('/', \App\Controllers\Common\RedirectController::class . ':adminRoot');
        
        // ==================== 页面路由 ====================
        $group->get('/login', \App\Controllers\Common\PageController::class . ':adminLogin');
        $group->get('/dashboard', \App\Controllers\Common\PageController::class . ':adminDashboard');
        $group->get('/users', \App\Controllers\Common\PageController::class . ':adminUsers');
        $group->get('/members', \App\Controllers\Common\PageController::class . ':adminMembers');
        $group->get('/units', \App\Controllers\Common\PageController::class . ':adminUnits');
        
        // ==================== 认证接口 ====================
        $group->post('/auth/login', \App\Controllers\Admin\AuthController::class . ':login');

        // ==================== API 路由（需要 JWT 认证）====================
        $group->group('/api', function (RouteCollectorProxy $inner) {
            // 管理员管理
            $inner->get('/users', \App\Controllers\Admin\UserController::class . ':index');
            $inner->get('/users/{id}', \App\Controllers\Admin\UserController::class . ':show');
            $inner->post('/users', \App\Controllers\Admin\UserController::class . ':store');
            $inner->put('/users/{id}', \App\Controllers\Admin\UserController::class . ':update');
            $inner->delete('/users/{id}', \App\Controllers\Admin\UserController::class . ':destroy');
            $inner->post('/users/batch', \App\Controllers\Admin\BatchUserController::class . ':batch');
            
            // 用户管理（小程序用户）
            $inner->get('/members', \App\Controllers\Admin\MemberController::class . ':index');
            $inner->get('/members/{id}', \App\Controllers\Admin\MemberController::class . ':show');
            $inner->put('/members/{id}', \App\Controllers\Admin\MemberController::class . ':update');
            $inner->delete('/members/{id}', \App\Controllers\Admin\MemberController::class . ':destroy');
            $inner->post('/members/batch', \App\Controllers\Admin\BatchMemberController::class . ':batch');
            
            // 单位管理
            $inner->get('/units', \App\Controllers\Admin\UnitController::class . ':index');
            $inner->get('/units/{id}', \App\Controllers\Admin\UnitController::class . ':show');
            $inner->post('/units', \App\Controllers\Admin\UnitController::class . ':store');
            $inner->put('/units/{id}', \App\Controllers\Admin\UnitController::class . ':update');
            $inner->delete('/units/{id}', \App\Controllers\Admin\UnitController::class . ':destroy');
            $inner->post('/units/batch', \App\Controllers\Admin\BatchUnitController::class . ':batch');
            
            // 文件上传
            $inner->post('/upload/image', \App\Controllers\Common\UploadController::class . ':image');
            $inner->post('/upload/file', \App\Controllers\Common\UploadController::class . ':file');
        })->add(new JwtAuthMiddleware('admin'));
    });
};
