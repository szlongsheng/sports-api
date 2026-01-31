<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class PageService
{
    public function paginate(Builder $query, int $page = 1, int $perPage = 20): array
    {
        $total = $query->count();
        $perPage = max(1, min(100, $perPage));
        $page = max(1, $page);
        $totalPages = (int) ceil($total / $perPage);
        $list = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        return [
            'list'        => $list,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ];
    }

    public function fromRequest(array $params, int $defaultPerPage = 20): array
    {
        return [
            'page'     => max(1, (int) ($params['page'] ?? 1)),
            'per_page' => max(1, min(100, (int) ($params['per_page'] ?? $defaultPerPage))),
        ];
    }
}
