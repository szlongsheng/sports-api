<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Campaign;
use App\Models\User;
use App\Services\PageService;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 单位端 - 用户管理（本单位的 users）
 */
class UnitUserController
{
    use HasFilters;

    protected PageService $pageService;
    protected array $filterMap = [
        'keyword' => ['field' => 'nickname', 'op' => 'like'],
        'phone'   => ['field' => 'phone', 'op' => 'like'],
    ];
    protected array $orderFields = ['id', 'nickname', 'phone', 'created_at'];
    protected string $defaultOrder = 'id';
    protected string $defaultOrderDir = 'desc';

    public function __construct()
    {
        $this->pageService = new PageService();
    }

    protected function getUnitId(ServerRequestInterface $request): ?int
    {
        $user = $request->getAttribute('user');
        return isset($user['id']) ? (int) $user['id'] : null;
    }

    protected function buildQuery(array $params, int $unitId): Builder
    {
        $query = User::query()->where('unit_id', $unitId);
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

    protected function applyOrder(Builder $query, array $params): void
    {
        $orderBy = $params['order_by'] ?? $params['orderBy'] ?? $this->defaultOrder;
        $orderDir = strtolower($params['order_dir'] ?? $params['orderDir'] ?? $this->defaultOrderDir);
        if (!in_array($orderBy, $this->orderFields, true)) {
            $orderBy = $this->defaultOrder;
        }
        $orderDir = $orderDir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($orderBy, $orderDir);
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }

        $params = array_merge($request->getQueryParams(), $request->getParsedBody() ?? []);
        $query = $this->buildQuery($params, $unitId);
        $this->applyFilters($query, $params);
        $this->applyOrder($query, $params);

        $paging = $this->pageService->fromRequest($params);
        $result = $this->pageService->paginate($query, $paging['page'], $paging['per_page']);

        $response->getBody()->write(json_encode(json_success($result)));
        return $response;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }

        $user = User::where('id', $args['id'] ?? 0)->where('unit_id', $unitId)->first();
        if (!$user) {
            $response->getBody()->write(json_encode(json_error(404, '用户不存在')));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(json_success($user)));
        return $response;
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }

        $user = User::where('id', $args['id'] ?? 0)->where('unit_id', $unitId)->first();
        if (!$user) {
            $response->getBody()->write(json_encode(json_error(404, '用户不存在')));
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody() ?? [];
        $fillable = ['nickname', 'phone'];
        $updateData = array_intersect_key($data, array_flip($fillable));
        $user->update($updateData);

        $response->getBody()->write(json_encode(json_success($user->fresh(), '更新成功')));
        return $response;
    }
}
