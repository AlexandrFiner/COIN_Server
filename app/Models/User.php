<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'login', 'password', 'api_token', 'provider', 'balance_coin', 'mining_speed', 'group_vk', 'clan_id', 'online', 'decoration_avatar', 'decoration_frame'
    ];

    protected $hidden = [
        'password', 'api_token', 'created_at', 'updated_at'
    ];
}
