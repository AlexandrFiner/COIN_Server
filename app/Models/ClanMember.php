<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClanMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'clan_id', 'user_id', 'role'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
