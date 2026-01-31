<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BaseCrudController;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 用户管理 - 小程序用户（users 表）
 */
class MemberController extends BaseCrudController
{
    protected string $modelClass = User::class;
    protected array $filterMap = [
        'keyword' => ['field' => 'nickname', 'op' => 'like'],
        'unit_id' => 'unit_id',
        'phone'   => ['field' => 'phone', 'op' => 'like'],
    ];
    protected array $orderFields = ['id', 'nickname', 'phone', 'created_at'];

    protected function buildQuery(array $params): Builder
    {
        $query = parent::buildQuery($params)->with('unit');
        if (isset($params['keyword']) && $params['keyword'] !== '') {
            $kw = $params['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('nickname', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%")
                    ->orWhere('openid', 'like', "%{$kw}%");
            });
        }
        return $query;
    }

    /**
     * 小程序用户由微信登录创建，管理端仅支持查看/编辑/删除，不支持创建
     */
    protected function createModel(array $data): ?Model
    {
        return null;
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write(json_encode(json_error(422, '小程序用户由微信登录自动创建，不支持手动添加')));
        return $response->withStatus(422);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $model = $this->getModel()::find($args['id'] ?? 0);
        if (!$model) {
            $response->getBody()->write(json_encode(json_error(404, '记录不存在')));
            return $response->withStatus(404);
        }
        $data = $request->getParsedBody() ?? [];
        if (array_key_exists('unit_id', $data) && ($data['unit_id'] === '' || $data['unit_id'] === null)) {
            $data['unit_id'] = null;
        }
        $fillable = $model->getFillable();
        $updateData = array_intersect_key($data, array_flip($fillable));
        $model->update($updateData);
        $response->getBody()->write(json_encode(json_success($model->fresh()->load('unit'), '更新成功')));
        return $response;
    }
}
