<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    //
    public function index(Request $request) {
        return response()->json(["response" => [
            "message" => "Поздравляем, вы модератор!"
        ]], 200);
    }
}
