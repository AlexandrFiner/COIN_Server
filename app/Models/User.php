<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'login', 'password', 'provider', 'api_token', 'balance_coin', 'mining_speed'
    ];

    protected $hidden = [
        'password', 'api_token'
    ];
}
