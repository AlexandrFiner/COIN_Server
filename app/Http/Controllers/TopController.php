<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class TopController extends Controller
{
    public function get(Route $route) {
        $users = User::orderBy('balance_coin', 'DESC')->limit(15)->get();
        return response()->json(["response" => $users], 200);
    }
}
