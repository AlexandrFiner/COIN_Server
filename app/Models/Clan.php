<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'owner_id', 'closed', 'slots'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
