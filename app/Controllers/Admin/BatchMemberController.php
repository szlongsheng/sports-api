<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BatchController;
use App\Models\User;

/**
 * 用户管理（小程序用户）仅支持批量删除
 */
class BatchMemberController extends BatchController
{
    protected string $modelClass = User::class;

    protected function executeBatch(string $action, array $ids, array $data): int
    {
        if ($action !== 'delete') {
            throw new \RuntimeException('用户管理仅支持批量删除');
        }
        return parent::executeBatch($action, $ids, $data);
    }
}
