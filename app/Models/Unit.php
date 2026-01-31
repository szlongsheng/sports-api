<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Unit extends Model
{
    protected $table = 'units';
    protected $fillable = ['account', 'password', 'name', 'contact', 'phone', 'status'];
    protected $hidden = ['password'];

    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }
}
