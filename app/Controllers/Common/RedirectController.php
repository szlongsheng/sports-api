<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RedirectController
{
    /**
     * 管理端根路径重定向
     * 已登录 -> /admin/dashboard
     * 未登录 -> /admin/login
     */
    public function adminRoot(Request $request, Response $response): Response
    {
        // 检查是否有 token（从 cookie 或 localStorage 传来的 Authorization header）
        $authHeader = $request->getHeaderLine('Authorization');
        $hasToken = !empty($authHeader) && str_starts_with($authHeader, 'Bearer ');
        
        // 如果通过 HTTP 请求且没有 token，检查前端是否存储了 token
        // 由于无法直接访问 localStorage，我们返回一个 HTML 页面来做判断
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转中...</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f1419 0%, #1a2332 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .loading {
            text-align: center;
            color: #10b981;
        }
        .spinner {
            width: 50px;
            height: 50px;
            margin: 0 auto 20px;
            border: 4px solid rgba(16, 185, 129, 0.1);
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="spinner"></div>
        <p>正在跳转...</p>
    </div>
    <script>
        // 检查是否有管理端 token（与单位端分离，避免混用）
        const token = localStorage.getItem('admin_token');
        if (token) {
            window.location.href = '/admin/dashboard';
        } else {
            window.location.href = '/admin/login';
        }
    </script>
</body>
</html>
HTML;

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * 单位端根路径重定向
     * 已登录 -> /unit/dashboard
     * 未登录 -> /unit/login
     */
    public function unitRoot(Request $request, Response $response): Response
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转中...</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f1419 0%, #1a2332 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .loading {
            text-align: center;
            color: #10b981;
        }
        .spinner {
            width: 50px;
            height: 50px;
            margin: 0 auto 20px;
            border: 4px solid rgba(16, 185, 129, 0.1);
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="spinner"></div>
        <p>正在跳转...</p>
    </div>
    <script>
        // 检查是否有单位端 token（与管理端分离，避免混用）
        const token = localStorage.getItem('unit_token');
        if (token) {
            window.location.href = '/unit/dashboard';
        } else {
            window.location.href = '/unit/login';
        }
    </script>
</body>
</html>
HTML;

        $response->getBody()->write($html);
        return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
