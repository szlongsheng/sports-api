<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['openid', 'unionid', 'nickname', 'avatar', 'phone', 'unit_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
