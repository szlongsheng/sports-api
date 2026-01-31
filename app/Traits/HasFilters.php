<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    /**
     * 应用筛选条件到查询
     * 子类重写 $filterMap 定义字段映射
     *
     * @param Builder $query
     * @param array $params 来自 query string 或 request body
     */
    protected function applyFilters(Builder $query, array $params): void
    {
        $map = $this->filterMap ?? [];

        foreach ($map as $paramKey => $config) {
            $value = $params[$paramKey] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            if (is_string($config)) {
                $query->where($config, $value);
                continue;
            }

            $field = $config['field'] ?? $paramKey;
            $op = $config['op'] ?? '=';

            switch ($op) {
                case 'like':
                    $query->where($field, 'like', '%' . $value . '%');
                    break;
                case 'in':
                    $arr = is_array($value) ? $value : explode(',', (string) $value);
                    $query->whereIn($field, $arr);
                    break;
                case 'between':
                    if (is_array($value) && count($value) >= 2) {
                        $query->whereBetween($field, [$value[0], $value[1]]);
                    }
                    break;
                default:
                    $query->where($field, $op, $value);
            }
        }
    }
}
