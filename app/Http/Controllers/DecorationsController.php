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
        $userId = isset($request['id']) ? $request['id'] : 0;
        if($userId) {
            try {
                $user_request = User::where('login', '=', $userId)->firstOrFail();
            } catch (\Exception $e) {
                return Response::error(404, "User not found");
            }
        }

        $result = [];

        switch ($type) {
            case 'avatars': {
                if($userId)
                    $result = [];
                else
                    $result = Decoration::where('type', '=', 'avatar')->get();
                break;
            }
            case 'frames': {
                if($userId)
                    $result = UserDecoration::where('user_decorations.user_id', '=', $user_request->id)
                        ->leftJoin('decorations', 'decorations.id', '=', 'user_decorations.decoration_id')
                        ->select('decorations.*')
                        ->get();
                else
                    $result = Decoration::where('type', '=', 'frames')->get();
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
            $result = Decoration::where('id', '=', $item_id)->firstOrFail();
            return Response::success([
                'item' => $result,
                'isHas' => $this->isHas($request->user()->id, $item_id)
            ]);
        } catch (\Exception $e) {
            return Response::error(404, 'Item dont exists');
        }
    }

    private function isHas($user_id, $item_id) {
        try {
            echo $user_id.' '.$item_id;
            UserDecoration::where('user_decorations.user_id', '=', $user_id)->where('user_decorations.decoration_id', '=', $item_id)->firstOrFail();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
