<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Campaign;
use App\Models\Prize;
use App\Services\PageService;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 单位端 - 奖品管理
 */
class UnitPrizeController
{
    use HasFilters;

    protected PageService $pageService;
    protected array $filterMap = [
        'keyword' => ['field' => 'name', 'op' => 'like'],
        'status'  => 'status',
    ];
    protected array $orderFields = ['id', 'name', 'sort', 'points_required', 'created_at'];
    protected string $defaultOrder = 'sort';
    protected string $defaultOrderDir = 'asc';

    public function __construct()
    {
        $this->pageService = new PageService();
    }

    protected function getUnitId(ServerRequestInterface $request): ?int
    {
        $user = $request->getAttribute('user');
        return isset($user['id']) ? (int) $user['id'] : null;
    }

    protected function getCampaignId(ServerRequestInterface $request): ?int
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) return null;
        $campaign = Campaign::where('unit_id', $unitId)->first();
        return $campaign ? (int) $campaign->id : null;
    }

    protected function buildQuery(array $params, int $campaignId): Builder
    {
        return Prize::query()->where('campaign_id', $campaignId);
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
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $params = array_merge($request->getQueryParams(), $request->getParsedBody() ?? []);
        $query = $this->buildQuery($params, $campaignId);
        $this->applyFilters($query, $params);
        $this->applyOrder($query, $params);

        $paging = $this->pageService->fromRequest($params);
        $result = $this->pageService->paginate($query, $paging['page'], $paging['per_page']);

        $response->getBody()->write(json_encode(json_success($result)));
        return $response;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $prize = Prize::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$prize) {
            $response->getBody()->write(json_encode(json_error(404, '奖品不存在')));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(json_success($prize)));
        return $response;
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $data = $request->getParsedBody() ?? [];
        if (empty($data['name'])) {
            $response->getBody()->write(json_encode(json_error(422, '奖品名称不能为空')));
            return $response->withStatus(422);
        }
        if (empty($data['points_required']) || (int) $data['points_required'] < 0) {
            $response->getBody()->write(json_encode(json_error(422, '所需积分不能为空且须≥0')));
            return $response->withStatus(422);
        }

        $fillable = [
            'campaign_id', 'name', 'image', 'images', 'description',
            'points_required', 'stock', 'per_user_limit', 'sort', 'status',
        ];
        $createData = array_intersect_key($data, array_flip($fillable));
        $createData['campaign_id'] = $campaignId;
        $createData['points_required'] = (int) ($createData['points_required'] ?? 0);
        $createData['stock'] = (int) ($createData['stock'] ?? 0);
        $createData['per_user_limit'] = (int) ($createData['per_user_limit'] ?? 0);
        $createData['sort'] = (int) ($createData['sort'] ?? 0);
        $createData['status'] = (int) ($createData['status'] ?? 1);

        if (isset($createData['images']) && !is_array($createData['images'])) {
            $createData['images'] = is_string($createData['images']) ? json_decode($createData['images'], true) : [];
        }

        $prize = Prize::create($createData);
        $response->getBody()->write(json_encode(json_success($prize, '创建成功')));
        return $response->withStatus(201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $prize = Prize::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$prize) {
            $response->getBody()->write(json_encode(json_error(404, '奖品不存在')));
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody() ?? [];
        $fillable = [
            'name', 'image', 'images', 'description',
            'points_required', 'stock', 'per_user_limit', 'sort', 'status',
        ];
        $updateData = array_intersect_key($data, array_flip($fillable));

        if (isset($updateData['images']) && !is_array($updateData['images'])) {
            $updateData['images'] = is_string($updateData['images']) ? json_decode($updateData['images'], true) : [];
        }
        if (array_key_exists('points_required', $updateData)) {
            $updateData['points_required'] = (int) $updateData['points_required'];
        }
        if (array_key_exists('stock', $updateData)) {
            $updateData['stock'] = (int) $updateData['stock'];
        }
        if (array_key_exists('per_user_limit', $updateData)) {
            $updateData['per_user_limit'] = (int) $updateData['per_user_limit'];
        }
        if (array_key_exists('sort', $updateData)) {
            $updateData['sort'] = (int) $updateData['sort'];
        }

        $prize->update($updateData);
        $response->getBody()->write(json_encode(json_success($prize->fresh(), '更新成功')));
        return $response;
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $prize = Prize::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$prize) {
            $response->getBody()->write(json_encode(json_error(404, '奖品不存在')));
            return $response->withStatus(404);
        }

        $prize->delete();
        $response->getBody()->write(json_encode(json_success(null, '删除成功')));
        return $response;
    }
}
