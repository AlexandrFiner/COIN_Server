<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'login', 'password', 'provider', 'api_token', 'balance_coin', 'mining_speed', 'group_vk'
    ];

    protected $hidden = [
        'password', 'api_token', 'created_at', 'updated_at'
    ];
}
