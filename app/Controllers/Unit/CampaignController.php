<?php

declare(strict_types=1);

namespace App\Controllers\Unit;

use App\Models\Campaign;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CampaignController
{
    protected function getUnitId(ServerRequestInterface $request): ?int
    {
        $user = $request->getAttribute('user');
        return isset($user['id']) ? (int) $user['id'] : null;
    }

    /**
     * 获取当前单位的活动（单位仅一个活动）
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }

        $campaign = Campaign::where('unit_id', $unitId)->first();

        $response->getBody()->write(json_encode(json_success($campaign)));
        return $response;
    }

    /**
     * 保存活动（不存在则创建，存在则更新）
     */
    public function save(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $unitId = $this->getUnitId($request);
        if (!$unitId) {
            $response->getBody()->write(json_encode(json_error(401, '未登录')));
            return $response->withStatus(401);
        }

        $data = $request->getParsedBody() ?? [];
        
        if (empty($data['name'])) {
            $response->getBody()->write(json_encode(json_error(422, '活动名称不能为空')));
            return $response->withStatus(422);
        }

        $campaign = Campaign::where('unit_id', $unitId)->first();

        $fillable = [
            'name', 'start_at', 'end_at', 'points_rules',
            'points_multiplier', 'exchange_review', 'invite_code',
            'detail_content', 'detail_images', 'rules_content', 'splash_image',
            'status',
        ];
        $updateData = array_intersect_key($data, array_flip($fillable));

        if ($campaign) {
            $campaign->update($updateData);
            $campaign = $campaign->fresh();
            $message = '保存成功';
        } else {
            $updateData['unit_id'] = $unitId;
            $campaign = Campaign::create($updateData);
            $message = '创建成功';
        }

        $response->getBody()->write(json_encode(json_success($campaign, $message)));
        return $response;
    }
}
