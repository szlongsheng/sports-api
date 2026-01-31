<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Common\BaseCrudController;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UnitController extends BaseCrudController
{
    protected string $modelClass = Unit::class;
    protected array $filterMap = [
        'keyword' => ['field' => 'account', 'op' => 'like'],
        'status'  => 'status',
    ];
    protected array $orderFields = ['id', 'account', 'name', 'created_at'];

    protected function buildQuery(array $params): Builder
    {
        $query = parent::buildQuery($params);
        if (isset($params['keyword']) && $params['keyword'] !== '') {
            $kw = $params['keyword'];
            $query->where(function ($q) use ($kw) {
                $q->where('account', 'like', "%{$kw}%")
                    ->orWhere('name', 'like', "%{$kw}%")
                    ->orWhere('contact', 'like', "%{$kw}%")
                    ->orWhere('phone', 'like', "%{$kw}%");
            });
        }
        return $query;
    }

    protected function createModel(array $data): ?Model
    {
        if (empty($data['account']) || empty($data['password'])) {
            return null;
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        return Unit::create($data);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $model = $this->getModel()::find($args['id'] ?? 0);
        if (!$model) {
            $response->getBody()->write(json_encode(json_error(404, '记录不存在')));
            return $response->withStatus(404);
        }
        $data = $request->getParsedBody() ?? [];
        $fillable = $model->getFillable();
        $updateData = array_intersect_key($data, array_flip($fillable));
        if (isset($updateData['password']) && $updateData['password'] !== '') {
            $updateData['password'] = password_hash($updateData['password'], PASSWORD_BCRYPT);
        } else {
            unset($updateData['password']);
        }
        $model->update($updateData);
        $response->getBody()->write(json_encode(json_success($model->fresh(), '更新成功')));
        return $response;
    }
}
