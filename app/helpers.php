<?php

declare(strict_types=1);

/**
 * 统一 JSON 成功响应
 */
function json_success($data = null, string $message = 'success'): array
{
    return [
        'code'    => 0,
        'message' => $message,
        'data'    => $data,
    ];
}

/**
 * 统一 JSON 错误响应
 */
function json_error(int $code = 1, string $message = 'error', $data = null): array
{
    return [
        'code'    => $code,
        'message' => $message,
        'data'    => $data,
    ];
}

/**
 * 渲染视图（PHP 模板）
 */
function view(string $path, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    $fullPath = dirname(__DIR__) . '/resources/views/' . $path . '.php';
    if (file_exists($fullPath)) {
        include $fullPath;
    }
    return (string) ob_get_clean();
}
