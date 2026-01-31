<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prize extends Model
{
    protected $table = 'prizes';

    protected $fillable = [
        'campaign_id', 'name', 'image', 'images', 'description',
        'points_required', 'stock', 'per_user_limit', 'sort', 'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
