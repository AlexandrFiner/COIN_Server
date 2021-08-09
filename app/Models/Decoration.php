<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decoration extends Model
{
    use HasFactory;

    static function isUserHas($user_id, $item_id) {
        try {
            UserDecoration::where('user_decorations.user_id', $user_id)->where('user_decorations.decoration_id', $item_id)->firstOrFail();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
