<?php

namespace App\Http\Controllers;

use App\Models\Api\Response;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    //
    public function index(Request $request) {
        return Response::success(["message" => "Ура, ты модератор"]);
    }
}
