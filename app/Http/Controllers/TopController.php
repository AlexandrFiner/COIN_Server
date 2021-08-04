<?php

namespace App\Http\Controllers;

use App\Models\Api\Response;
use App\Models\Clan;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class TopController extends Controller
{
    public function get(Route $route): \Illuminate\Http\JsonResponse
    {
        $users = User::orderBy('balance_coin', 'DESC')->limit(15)->get();
        return Response::success($users);
    }

    public function getGroups(Route $route): \Illuminate\Http\JsonResponse
    {
        $groups = Group::orderBy('balance_coin', 'DESC')->limit(15)->get();
        return Response::success($groups);
    }

    public function getClans(Route $route): \Illuminate\Http\JsonResponse
    {
        $clans = Clan::orderBy('score', 'DESC')->limit(15)->get();
        return Response::success($clans);
    }
}
