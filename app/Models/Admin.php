<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Admin extends Model
{
    protected $table = 'admins';
    protected $fillable = ['username', 'password', 'name', 'status'];
    protected $hidden = ['password'];

    public function scopeStatus(Builder $query, int $status): Builder
    {
        return $query->where('status', $status);
    }
}
