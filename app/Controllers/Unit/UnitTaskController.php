<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Campaign;
use App\Models\CampaignCustomTask;
use App\Services\PageService;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 单位端 - 打卡任务管理（campaign_custom_tasks）
 */
class UnitTaskController
{
    use HasFilters;

    protected PageService $pageService;
    protected array $filterMap = [
        'keyword' => ['field' => 'name', 'op' => 'like'],
        'type'    => 'type',
        'status'  => 'status',
    ];
    protected array $orderFields = ['id', 'name', 'sort', 'created_at'];
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
        return CampaignCustomTask::query()->where('campaign_id', $campaignId);
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

        $task = CampaignCustomTask::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$task) {
            $response->getBody()->write(json_encode(json_error(404, '任务不存在')));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(json_success($task)));
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
            $response->getBody()->write(json_encode(json_error(422, '任务名称不能为空')));
            return $response->withStatus(422);
        }

        $fillable = [
            'campaign_id', 'type', 'name', 'cover_image', 'description', 'unit',
            'points_rule', 'need_media', 'need_review', 'sort', 'status',
        ];
        $createData = array_intersect_key($data, array_flip($fillable));
        $createData['campaign_id'] = $campaignId;
        $createData['type'] = $createData['type'] ?? 'custom';
        $createData['unit'] = $createData['unit'] ?? '次';
        $createData['need_media'] = $createData['need_media'] ?? 0;
        $createData['need_review'] = $createData['need_review'] ?? 0;
        $createData['sort'] = $createData['sort'] ?? 0;
        $createData['status'] = $createData['status'] ?? 1;

        if (isset($data['points_rule']) && !is_array($data['points_rule'])) {
            $createData['points_rule'] = is_string($data['points_rule']) ? json_decode($data['points_rule'], true) : $data['points_rule'];
        }

        $task = CampaignCustomTask::create($createData);
        $response->getBody()->write(json_encode(json_success($task, '创建成功')));
        return $response->withStatus(201);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $task = CampaignCustomTask::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$task) {
            $response->getBody()->write(json_encode(json_error(404, '任务不存在')));
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody() ?? [];
        $fillable = [
            'type', 'name', 'cover_image', 'description', 'unit',
            'points_rule', 'need_media', 'need_review', 'sort', 'status',
        ];
        $updateData = array_intersect_key($data, array_flip($fillable));

        if (isset($updateData['points_rule']) && !is_array($updateData['points_rule'])) {
            $updateData['points_rule'] = is_string($updateData['points_rule']) ? json_decode($updateData['points_rule'], true) : $updateData['points_rule'];
        }

        $task->update($updateData);
        $response->getBody()->write(json_encode(json_success($task->fresh(), '更新成功')));
        return $response;
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $task = CampaignCustomTask::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$task) {
            $response->getBody()->write(json_encode(json_error(404, '任务不存在')));
            return $response->withStatus(404);
        }

        $task->delete();
        $response->getBody()->write(json_encode(json_success(null, '删除成功')));
        return $response;
    }
}
