<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function index(Request $request) {
        return response()->json(["response" => [
            "message" => "Поздравляем, вы администратор!"
        ]], 200);
    }
}
