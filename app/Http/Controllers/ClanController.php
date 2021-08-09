<?php

namespace App\Http\Controllers;

use App\Models\Clan;
use App\Models\ClanMember;
use App\Models\User;
use App\Models\Api\Response;
use Illuminate\Http\Request;

class ClanController extends Controller
{
    public function index(Request $request) {
        $clan_id = $request->user()->clan_id;
        $clan = $this->getClanData($request, isset($request['id']) ? $request['id'] : 0);
        $clan_member = $this->getMemberData($request, $clan);
        $role = $clan_member['role'];

        $clanMembers = User::where('clan_id', '=', $clan_id)->orderBy('id', 'DESC')->take(50)->get()->mapWithKeys(function ($item) {
            return [(int)$item['login'] => $item];
        })->toArray();

        return Response::success([
            'clan' => $clan,
            'members' => $clanMembers,
            'role' => $role,
        ]);
    }

    public function create(Request $request) {
        if($request->user()->clan_id)
            return Response::error(101, "User already in clan" , 200);

        $title = isset($request['title']) ? $request['title'] : null;
        $description = isset($request['description']) ? $request['description'] : null;
        $closed = isset($request['closed']) ? (bool)$request['closed'] : false;
        $logo = isset($request['logo']) ? $request['logo'] : "";

        if(empty($title))
            return Response::error(102, "Params not sended" , 200);

        $logo_url = "";
        if($logo) {
            $logo = str_replace('data:image/png;base64,', '', $logo);
            $logo = str_replace(' ', '+', $logo);

            $file = base64_decode($logo);
            $file_name = md5(time()).'.png';
            $logo_url = public_path().'/upload/clans/'.$file_name;
            if(!file_put_contents($logo_url, $file))
                $logo_url = "";
            else
                $logo_url = env('APP_URL').'/upload/clans/'.$file_name;
        }

        $clan = Clan::create([
            'avatar' => $logo_url,
            'owner_id' => $request->user()->id,
            'title' => $title,
            'description' => $description,
            'closed' => $closed
        ]);

        ClanMember::create([
            'clan_id' => $clan->id,
            'user_id' => $request->user()->id,
            'role' => 'owner'
        ]);

        $request->user()->update(["clan_id" => $clan->id]);

        return Response::success($clan);
    }

    public function search(Request $request) {
        $search = isset($request['search']) ? $request['search'] : null;

        if(empty($search))
            return Response::error(101, "Params not sended");

        $clans = Clan::where([['title','LIKE',"%".$search."%"]])->orderBy('id', 'DESC')->take(5)->get();
        return response()->json(['response' => $clans], 200);
    }

    public function uploadAvatar(Request $request) {
        $logo = isset($request['logo']) ? $request['logo'] : "";
        $clan = $this->getClanData($request, isset($request['id']) ? $request['id'] : 0);
        $clan_member = $this->getMemberData($request, $clan);
        $role = $clan_member['role'];

        if($role !== 'admin' && $role !== 'moderator' && $role !== 'owner')
            return Response::error(403, "Access denied");

        // TODO: Удаление старой аватарки

        $logo_url = "";
        if($logo) {
            $logo = str_replace('data:image/png;base64,', '', $logo);
            $logo = str_replace(' ', '+', $logo);

            $file = base64_decode($logo);
            $file_name = md5(time()).'.png';
            $logo_url = public_path().'/upload/clans/'.$file_name;
            if(!file_put_contents($logo_url, $file))
                $logo_url = "";
            else
                $logo_url = env('APP_URL').'/upload/clans/'.$file_name;
        }

        $clan->update(["avatar" => $logo_url]);

        return Response::success(['clan' => $clan]);
    }

    public function update(Request $request) {
        $title = isset($request['title']) ? $request['title'] : "";
        $description = isset($request['description']) ? $request['description'] : "";

        if(empty($title) || strlen($title) > 64 || strlen($description) > 256)
            return Response::error(403, "Wrong params");

        $clan = $this->getClanData($request, isset($request['id']) ? $request['id'] : 0);
        $clan_member = $this->getMemberData($request, $clan);
        $role = $clan_member['role'];

        if($role !== 'admin' && $role !== 'moderator' && $role !== 'owner')
            return Response::error(403, "Access denied");

        $clan->update(["title" => $title, "description" => $description,]);
        return Response::success(["clan" => $clan]);
    }

    public function getUsers(Request $request) {
        $type = $request['type'];
        $limit = isset($request['limit']) ? $request['limit'] : 20;
        $offset = isset($request['offset']) ? $request['offset'] : 0;
        $clan = $this->getClanData($request, isset($request['id']) ? $request['id'] : 0);
        $clan_member = $this->getMemberData($request, $clan);

        $role = $clan_member['role'];
        $users = [];
        $load_more = false;
        $total = 0;
        switch ($type) {
            case 'admins': {
                if($role !== 'owner' && $role !== 'moderator') {
                    return response()->json([
                        'error' => [
                            'error_code' => 404,
                            'error_message' => 'Access denied!'
                        ]
                    ], 200);
                }

                $users = ClanMember::where('clan_id', '=', $clan->id)
                    ->offset($offset)
                    ->limit($limit)
                    ->get();

                break;
            }
            default: {
                $total = ClanMember::where('clan_id', '=', $clan->id)->count();

                $users = ClanMember::where('clan_members.clan_id', '=', $clan->id)
                    ->orderBy('clan_members.user_id', 'asc')
                    ->offset($offset)
                    ->limit($limit)
                    ->leftJoin('users', 'users.id', '=', 'clan_members.user_id')
                    ->select('users.login', 'clan_members.*')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [(int)$item['login'] => $item];
                    })->toArray();

                $offset += $limit;
                $load_more = $total > $offset;
            }
        }
        return response()->json([
            'response' => [
                'users' => $users,
                'total' => $total,
                'offset' => $offset,
                'load_more' => $load_more
            ]
        ]);
    }


    /// функции

    private function getClanData(Request $request, $id = 0) {
        $clan_id = $request->user()->clan_id;
        if($id) {
            // Передача данныех
            try {
                $clan = Clan::findOrFail($id);
            } catch (\Exception $e) {
                exit(Response::error(404, "Clan not found"));
            }
        } else {
            if($clan_id < 1)
                exit(Response::error(404, "User not in clan"));

            try {
                $clan = Clan::findOrFail($clan_id);
            } catch (\Exception $e) {
                $request->user()->update(["clan_id" => 0]);
                exit(Response::error(404, "Clan not clan"));
            }
        }
        return $clan;
    }

    private function getMemberData(Request $request, $clan) {
        try {
            $clan_member = ClanMember::where('user_id', '=', $request->user()->id)->where('clan_id', '=', $clan->id)->firstOrFail();
        } catch (\Exception $e) {
            $request->user()->update([
                "clan_id" => 0
            ]);

            exit(Response::error(404, "User not in clan_member!"));
        }
        return $clan_member;
    }
}
