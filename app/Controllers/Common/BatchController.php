<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BatchController
{
    protected string $modelClass;

    public function batch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $action = $data['action'] ?? '';
        $ids = $data['ids'] ?? [];

        if (empty($action) || empty($ids)) {
            $response->getBody()->write(json_encode(json_error(400, '缺少 action 或 ids')));
            return $response->withStatus(400);
        }

        if (!is_array($ids)) {
            $ids = array_map('intval', explode(',', (string) $ids));
        }
        $ids = array_filter(array_map('intval', $ids));

        if (empty($ids)) {
            $response->getBody()->write(json_encode(json_error(400, 'ids 无效')));
            return $response->withStatus(400);
        }

        try {
            $count = $this->executeBatch($action, $ids, $data);
            $response->getBody()->write(json_encode(json_success(['affected' => $count], '操作成功')));
            return $response;
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode(json_error(500, $e->getMessage())));
            return $response->withStatus(500);
        }
    }

    protected function executeBatch(string $action, array $ids, array $data): int
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::whereIn('id', $ids);

        switch ($action) {
            case 'delete':
                return $query->delete();
            case 'enable':
                return $this->updateStatus($query, 1);
            case 'disable':
                return $this->updateStatus($query, 0);
            default:
                return $this->customBatchAction($action, $ids, $data);
        }
    }

    protected function updateStatus($query, int $status): int
    {
        $model = new $this->modelClass();
        if (!in_array('status', $model->getFillable(), true)) {
            throw new \RuntimeException('该模型不支持状态批量更新');
        }
        return $query->update(['status' => $status]);
    }

    /**
     * 子类可重写以支持自定义批量操作
     */
    protected function customBatchAction(string $action, array $ids, array $data): int
    {
        throw new \RuntimeException("不支持的批量操作: {$action}");
    }
}
