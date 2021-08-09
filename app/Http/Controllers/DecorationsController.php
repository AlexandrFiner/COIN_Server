<?php

namespace App\Http\Controllers;

use App\Models\Api\Response;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Decoration;
use App\Models\UserDecoration;

class DecorationsController extends Controller
{
    //
    public function index(Request $request) {
        $type = isset($request['type']) ? $request['type'] : 'all';
        $result = [];

        switch ($type) {
            case 'avatars': {
                $result = Decoration::where('type', 'avatar')->get();
                break;
            }
            case 'frames': {
                $result = Decoration::where('type', 'frames')->get();
                break;
            }
            default: {
                return Response::error(404, "Wrong type");
            }
        }
        return Response::success($result);
    }

    public function get(Request $request) {
        $item_id = isset($request['id']) ? $request['id'] : 0;
        try {
            $result = Decoration::where('id', $item_id)->firstOrFail();
            return Response::success([
                'item' => $result,
                'isHas' => Decoration::isUserHas($request->user()->id, $item_id)
            ]);
        } catch (\Exception $e) {
            return Response::error(404, 'Item dont exists');
        }
    }
}
