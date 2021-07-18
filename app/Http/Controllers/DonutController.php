<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DonutController extends Controller
{
    //
    public function index(Request $request) {
        return response()->json(["response" => [
            "message" => "Поздравляем, вы дон!"
        ]], 200);
    }
}
