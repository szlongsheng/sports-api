<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\CheckIn;
use App\Models\PointsLog;
use App\Services\PageService;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 单位端 - 审核打卡记录
 */
class UnitCheckInController
{
    use HasFilters;

    protected PageService $pageService;
    protected array $filterMap = [
        'status'   => 'status',
        'task_id'  => 'task_id',
        'user_id'  => 'user_id',
    ];
    protected array $orderFields = ['id', 'checked_at', 'created_at'];
    protected string $defaultOrder = 'checked_at';
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

    protected function getCampaignId(ServerRequestInterface $request): ?int
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) return null;
        $campaign = Campaign::where('unit_id', $unitId)->first();
        return $campaign ? (int) $campaign->id : null;
    }

    protected function buildQuery(array $params, int $campaignId): Builder
    {
        return CheckIn::query()
            ->where('campaign_id', $campaignId)
            ->with(['user', 'task']);
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

        $checkIn = CheckIn::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->with(['user', 'task'])
            ->first();
        if (!$checkIn) {
            $response->getBody()->write(json_encode(json_error(404, '打卡记录不存在')));
            return $response->withStatus(404);
        }

        $response->getBody()->write(json_encode(json_success($checkIn)));
        return $response;
    }

    /**
     * 审核通过
     */
    public function approve(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->review($request, $response, $args, CheckIn::STATUS_APPROVED);
    }

    /**
     * 审核拒绝
     */
    public function reject(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->review($request, $response, $args, CheckIn::STATUS_REJECTED);
    }

    protected function review(ServerRequestInterface $request, ResponseInterface $response, array $args, int $newStatus): ResponseInterface
    {
        $campaignId = $this->getCampaignId($request);
        if (!$campaignId) {
            $response->getBody()->write(json_encode(json_error(422, '请先创建活动')));
            return $response->withStatus(422);
        }

        $checkIn = CheckIn::where('id', $args['id'] ?? 0)
            ->where('campaign_id', $campaignId)
            ->first();
        if (!$checkIn) {
            $response->getBody()->write(json_encode(json_error(404, '打卡记录不存在')));
            return $response->withStatus(404);
        }

        if ($checkIn->status !== CheckIn::STATUS_PENDING) {
            $response->getBody()->write(json_encode(json_error(422, '该记录已审核，无需重复操作')));
            return $response->withStatus(422);
        }

        $checkIn->status = $newStatus;

        if ($newStatus === CheckIn::STATUS_APPROVED) {
            $points = (int) ($checkIn->points ?? 0);
            $checkIn->points = $points;
            $checkIn->save();

            if ($points > 0) {
                PointsLog::create([
                    'campaign_id' => $checkIn->campaign_id,
                    'user_id'     => $checkIn->user_id,
                    'type'        => 'checkin',
                    'amount'      => $points,
                    'ref_type'    => 'check_in',
                    'ref_id'      => $checkIn->id,
                    'remark'      => '打卡审核通过',
                ]);
                // 更新 campaign_members 积分冗余
                $member = CampaignMember::where('campaign_id', $checkIn->campaign_id)
                    ->where('user_id', $checkIn->user_id)
                    ->first();
                if ($member) {
                    $member->increment('points', $points);
                }
            }
        } else {
            $checkIn->save();
        }

        $msg = $newStatus === CheckIn::STATUS_APPROVED ? '已通过' : '已拒绝';
        $response->getBody()->write(json_encode(json_success($checkIn->fresh(), $msg)));
        return $response;
    }
}
