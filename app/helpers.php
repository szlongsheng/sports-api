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
 * 单位端侧边栏导航（传入当前路径标记 active）
 */
function unit_nav_items(string $activePath = ''): array
{
    $items = [
        ['label' => '仪表盘', 'url' => '/unit/dashboard'],
        ['label' => '单位信息', 'url' => '/unit/profile'],
        ['label' => '活动设置', 'url' => '/unit/campaign'],
        ['label' => '用户管理', 'url' => '/unit/users'],
        ['label' => '打卡任务', 'url' => '/unit/tasks'],
        ['label' => '审核打卡', 'url' => '/unit/checkins'],
        ['label' => '奖品管理', 'url' => '/unit/prizes'],
    ];
    foreach ($items as &$item) {
        $item['active'] = ($item['url'] === $activePath);
    }
    return $items;
}

/**
 * 管理端侧边栏导航（传入当前路径标记 active）
 */
function admin_nav_items(string $activePath = ''): array
{
    $items = [
        ['label' => '仪表盘', 'url' => '/admin/dashboard'],
        ['label' => '管理员', 'url' => '/admin/users'],
        ['label' => '用户管理', 'url' => '/admin/members'],
        ['label' => '单位管理', 'url' => '/admin/units'],
    ];
    foreach ($items as &$item) {
        $item['active'] = ($item['url'] === $activePath);
    }
    return $items;
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
