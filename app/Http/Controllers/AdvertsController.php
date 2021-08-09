<?php

namespace App\Http\Controllers;

use App\Models\Advert;
use App\Models\Api\Response;
use Illuminate\Http\Request;

class AdvertsController extends Controller
{
    //
    public function get(Request $request) {
        $type = isset($request['type']) ? $request['type'] : 'banner';
        $page = isset($request['page']) ? $request['page'] : 'main';

        $result = Advert::where('type', $type)
            ->where('place', $page)
            ->get();

        return Response::success($result);
    }
}
