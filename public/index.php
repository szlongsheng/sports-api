<?php

declare(strict_types=1);

// 三端统一入口：根据 PATH_INFO 或 Request URI 路由
// 管理端: /admin/*  单位端: /unit/*  小程序API: /api/*

use Slim\Factory\AppFactory;

$app = require __DIR__ . '/../bootstrap/app.php';

// 初始化数据库连接
$app->getContainer()->get('db');

$app->run();
