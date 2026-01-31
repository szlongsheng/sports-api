<?php

declare(strict_types=1);

use Slim\App;

/**
 * 主路由配置文件
 * 
 * 路由已拆分为三个独立文件：
 * - config/routes/admin.php  管理端路由
 * - config/routes/unit.php   单位端路由  
 * - config/routes/api.php    小程序API路由
 */
return function (App $app): void {
    // ==================== 全局路由 ====================
    // 根路径
    $app->get('/', \App\Controllers\Common\PingController::class . ':index');
    
    // 健康检查（三端通用）
    $app->get('/ping', \App\Controllers\Common\PingController::class . ':index');

    // DEBUG: 上传诊断（仅 APP_DEBUG=true 时有效，用于排查 $_FILES 为空问题）
    if (($_ENV['APP_DEBUG'] ?? '') === 'true') {
        $app->post('/debug/upload-check', function ($req, $res) {
            $files = $req->getUploadedFiles();
            $body = json_encode([
                'FILES_keys' => array_keys($_FILES),
                'FILES' => $_FILES,
                'getUploadedFiles_keys' => array_keys($files),
                'method' => $req->getMethod(),
                'content_type' => $req->getHeader('Content-Type')[0] ?? null,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $res->getBody()->write($body);
            return $res->withHeader('Content-Type', 'application/json');
        });
    }

    // ==================== 加载各端路由 ====================
    // 管理端路由
    (require __DIR__ . '/routes/admin.php')($app);
    
    // 单位端路由
    (require __DIR__ . '/routes/unit.php')($app);
    
    // 小程序API路由
    (require __DIR__ . '/routes/api.php')($app);
};
