<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Campaign extends Model
{
    protected $table = 'campaigns';

    protected $fillable = [
        'unit_id', 'name', 'start_at', 'end_at', 'points_rules',
        'points_multiplier', 'exchange_review', 'invite_code',
        'detail_content', 'detail_images', 'rules_content', 'splash_image',
        'status',
    ];

    protected $casts = [
        'points_rules' => 'array',
        'detail_images' => 'array',
        'points_multiplier' => 'float',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function scopeUnit(Builder $query, int $unitId): Builder
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }
}
