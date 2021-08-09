<?php

namespace App\Http\Controllers;

use App\Models\Api\Response;
use App\Models\Decoration;
use App\Models\Group;
use App\Models\User;
use App\Models\UserDecoration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function auth(Request $request) {
        try {
            $user = User::where('login', $request->user())->firstOrFail()->makeVisible(['api_token']);
        } catch (\Exception $e) {
            return Response::error(404, "Account does not exists");
        }

        $data = $request->request->all();
        $group_id = 0;
        if(isset($data['group'])) {
            // Если играет через группу, проверяем на существование в базе такой группы
            $group_id = $data['group']['id'];

            try {
                Group::where('group_id', $group_id)->firstOrFail();
            } catch (\Exception $e) {
                // Группы такой нет
                if(isset($data['group']['isAdmin'])) {
                    Group::create([
                        'group_id' => $group_id,
                        'admin_id' => $user->id,
                        'balance_coin' => 0
                    ]);
                } else {
                    $group_id = 0;
                    // TODO:: Группы еще нет
                }
            }
        }

        $token = Str::random(60);
        $user->update(["api_token" => $token, "group_vk" => $group_id]);

        return Response::success($user);
    }

    public function register(Request $request) {
        // Создание пользователя

        try {
            $user = User::create([
                'login' => $request->user(),
                'password' => Str::random(60),        // Пароль
                'provider' => 'vk',                         // Пока только VK
                'api_token' => Str::random(60),       // Создаем ключ API
                'online' => time(),                         // Был в сети последний раз
            ]);
        } catch (\Exception $e) {
            return Response::error(101, "Account already exists");
        }

        return Response::success($user->makeVisible(['api_token']));
    }

    public function get(Request $request) {
        // Получение информации о пользователе

        if(isset($request['id'])) {
            try {
                $user = User::where('login', $request['id'])->firstOrFail();
                if($user['decoration_avatar'])
                    $user['avatar'] = Decoration::where('id', $user['decoration_avatar'])->first();

                if($user['decoration_frame'])
                    $user['frame'] = Decoration::where('id', $user['decoration_frame'])->first();

            } catch (\Exception $e) {
                return Response::error(404, $e);
            }
        } else
            $user = $request->user();

        return Response::success($user);
    }

    public function earn(Request $request) {
        $params = $request->all();

        if(!isset($params['hash']))
            return Response::error(1000, "Wrong hash!");

        $hash = hash('sha256', $request->user()->api_token.'#CRYPT#alexfiner');

        if($hash !== $params['hash'])
            return Response::error(1000, "Wrong hash!");

        try {
            $token = Str::random(60);
            $request->user()->update([
                "api_token" => $token,
                'balance_coin' => $request->user()->balance_coin + $request->user()->mining_speed
            ]);
            // $user = User::
        } catch (\Exception $e) {
            return Response::error(404, $e);
        }

        return Response::success(200, $request->user()->makeVisible(['api_token']));
    }

    public function getDecorations(Request $request) {
        if(isset($request['id'])) {
            try {
                $user = User::where('login', $request['id'])->firstOrFail();
                /*
                if($user['decoration_avatar'])
                    $user['avatar'] = Decoration::where('id', $user['decoration_avatar'])->first();

                if($user['decoration_frame'])
                    $user['frame'] = Decoration::where('id', $user['decoration_frame'])->first();
                */
            } catch (\Exception $e) {
                return Response::error(404, $e);
            }
        } else
            $user = $request->user();

        $type = isset($request['type']) ? $request['type'] : 'all';
        $decorations = [];
        switch ($type) {
            case 'avatars': {
                $decorations = UserDecoration::where('user_decorations.user_id', $user->id)
                    ->leftJoin('decorations', 'decorations.id', '=', 'user_decorations.decoration_id')
                    ->select('decorations.*')
                    ->where('decorations.type', 'avatar')
                    ->get();
                break;
            }
            case 'frames': {
                $decorations = UserDecoration::where('user_decorations.user_id', $user->id)
                    ->leftJoin('decorations', 'decorations.id', '=', 'user_decorations.decoration_id')
                    ->select('decorations.*')
                    ->where('decorations.type', 'frame')
                    ->get();
                break;
            }
            default: {
                return Response::error(404, "Wrong type");
            }
        }

        return Response::success(["user" => $user, "decorations" => $decorations]);
    }

    public function setDecorations(Request $request) {
        /*
         * TODO: Переписать более красиво
         */

        $id = isset($request['id']) ? $request['id'] : 0;
        $type = isset($request['type']) ? $request['type'] : 'set';

        if($type === 'reset_frame') {
            $request->user()->decoration_frame = 0;
            $request->user()->update();
            return Response::success($request->user());
        }
        if($type === 'reset_avatar') {
            $request->user()->decoration_avatar = 0;
            $request->user()->update();
            return Response::success($request->user());
        }

        try {
            $decoration = Decoration::where('id', $id)->firstOrFail();
        } catch (\Exception $e) {
            return Response::error(404, "Decoration not found");
        }

        if(!Decoration::isUserHas($request->user()->id, $decoration->id))
            return Response::error(403, "User hasnt decoration");

        if($decoration->type === 'frame') {
            $request->user()->decoration_frame = $decoration->id;
            $request->user()->update();
            return Response::success($request->user());
        } else if($decoration->type === 'avatar') {
            $request->user()->decoration_avatar = $decoration->id;
            $request->user()->update();
            return Response::success($request->user());
        }

        return Response::error(500, "Wrong decoration type");
    }
}
