<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsLog extends Model
{
    protected $table = 'points_logs';

    protected $fillable = [
        'campaign_id', 'user_id', 'type', 'amount', 'ref_type', 'ref_id', 'remark',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
