<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\Container;

// 加载 Composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 加载环境变量
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// 创建依赖注入容器
$container = new Container();
AppFactory::setContainer($container);

// 创建 Slim 应用
$app = AppFactory::create();

// 注册服务
(require __DIR__ . '/../config/services.php')($container);

// 注册中间件
(require __DIR__ . '/../config/middleware.php')($app);

// 注册路由 - 根据请求路径分发到不同端
(require __DIR__ . '/../config/routes.php')($app);

return $app;
