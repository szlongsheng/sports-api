<?php
/**
 * PHP 内置服务器路由：将所有请求转发到 index.php
 * 用法: php -S localhost:8080 -t public public/router.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
// 存在的静态文件直接返回
if ($uri !== '/' && $uri !== '' && file_exists(__DIR__ . $uri)) {
    return false;
}
require __DIR__ . '/index.php';
