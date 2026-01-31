<?php

declare(strict_types=1);

namespace App\Controllers\Common;

use App\Services\PageService;
use App\Traits\HasFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseCrudController
{
    use HasFilters;

    protected PageService $pageService;
    protected string $modelClass;
    protected array $filterMap = [];
    protected array $orderFields = ['id'];
    protected string $defaultOrder = 'id';
    protected string $defaultOrderDir = 'desc';

    public function __construct()
    {
        $this->pageService = new PageService();
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody() ?? []);
        $query = $this->buildQuery($params);
        $this->applyFilters($query, $params);
        $this->applyOrder($query, $params);

        $paging = $this->pageService->fromRequest($params);
        $result = $this->pageService->paginate($query, $paging['page'], $paging['per_page']);

        $response->getBody()->write(json_encode(json_success($result)));
        return $response;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $model = $this->getModel()::find($args['id'] ?? 0);
        if (!$model) {
            $response->getBody()->write(json_encode(json_error(404, '记录不存在')));
            return $response->withStatus(404);
        }
        $response->getBody()->write(json_encode(json_success($model)));
        return $response;
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody() ?? [];
        $model = $this->createModel($data);
        if (!$model) {
            $response->getBody()->write(json_encode(json_error(422, '创建失败')));
            return $response->withStatus(422);
        }
        $response->getBody()->write(json_encode(json_success($model, '创建成功')));
        return $response->withStatus(201);
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
        $updateData = $fillable ? array_intersect_key($data, array_flip($fillable)) : $data;
        $model->update($updateData);
        $response->getBody()->write(json_encode(json_success($model->fresh(), '更新成功')));
        return $response;
    }

    public function destroy(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $model = $this->getModel()::find($args['id'] ?? 0);
        if (!$model) {
            $response->getBody()->write(json_encode(json_error(404, '记录不存在')));
            return $response->withStatus(404);
        }
        $model->delete();
        $response->getBody()->write(json_encode(json_success(null, '删除成功')));
        return $response;
    }

    protected function buildQuery(array $params): Builder
    {
        return $this->getModel()::query();
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

    protected function getModel(): Model
    {
        return new $this->modelClass();
    }

    abstract protected function createModel(array $data): ?Model;
}
