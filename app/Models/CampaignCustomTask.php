<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCustomTask extends Model
{
    protected $table = 'campaign_custom_tasks';

    protected $fillable = [
        'campaign_id', 'type', 'name', 'cover_image', 'description', 'unit',
        'points_rule', 'need_media', 'need_review', 'sort', 'status',
    ];

    protected $casts = [
        'points_rule' => 'array',
        'need_media'  => 'integer',
        'need_review' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
