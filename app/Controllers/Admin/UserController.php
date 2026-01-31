<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BaseCrudController;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends BaseCrudController
{
    protected string $modelClass = Admin::class;
    protected array $filterMap = [
        'keyword' => ['field' => 'username', 'op' => 'like'],
        'status'  => 'status',
    ];
    protected array $orderFields = ['id', 'username', 'created_at'];

    protected function buildQuery(array $params): Builder
    {
        $query = parent::buildQuery($params);
        if (isset($params['keyword']) && $params['keyword'] !== '') {
            $kw = $params['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('username', 'like', "%{$kw}%")
                    ->orWhere('name', 'like', "%{$kw}%");
            });
        }
        return $query;
    }

    protected function createModel(array $data): ?Model
    {
        if (empty($data['username']) || empty($data['password'])) {
            return null;
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return Admin::create($data);
    }
}
