<?php

namespace App\Http\Controllers;

use App\Models\Clan;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class ClanController extends Controller
{
    public function index(Request $request) {
        $clan_id = $request->user()->clan_id;

        if(isset($request['id'])) {
            // Передача данныех
            try {
                $clan = Clan::findOrFail($request['id']);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => [
                        'error_code' => 404,
                        'error_message' => 'Clan not found'
                    ]
                ], 200);
            }
        } else {
            if($clan_id < 1) {
                return response()->json([
                    'error' => [
                        'error_code' => 404,
                        'error_message' => 'User not in clan'
                    ]
                ], 200);
            }

            try {
                $clan = Clan::findOrFail($clan_id);
            } catch (\Exception $e) {
                $request->user()->update([
                    "clan_id" => 0
                ]);

                return response()->json([
                    'error' => [
                        'error_code' => 404,
                        'error_message' => 'Clan not found'
                    ]
                ], 200);
            }
        }

        $role = 'guest';
        if($clan_id === $clan->id) {
            $role = 'member';
            if($request->user()->id === $clan->owner_id) {
                $role = 'owner';
            }
        }
        try {
            $clanMembers = User::where('clan_id', '=', $clan_id)->orderBy('id', 'DESC')->take(50)->get();
        } catch (\Exception $e) {
            echo $e;
        }

        return response()->json(['response' => [
            'clan' => $clan,
            'members' => $clanMembers,
            'role' => $role,
        ]], 200);
    }

    public function create(Request $request) {
        $response = $request->all();

        if($request->user()->clan_id) {
            return response()->json([
                'error' => [
                    'error_code' => 101,
                    'error_message' => 'User already in clan'
                ]
            ], 200);
        }

        $title = isset($response['title']) ? $response['title'] : null;
        $description = isset($response['description']) ? $response['description'] : null;
        $closed = isset($response['closed']) ? (bool)$response['closed'] : false;
        $avatar = isset($response['avatar']) ? $response['avatar'] : "";

        if(empty($title)) {
            return response()->json([
                'error' => [
                    'error_code' => 102,
                    'error_message' => 'Params not sended'
                ]
            ], 200);
        }

        $clan = Clan::create([
            'avatar' => $avatar,
            'owner_id' => $request->user()->id,
            'title' => $title,
            'description' => $description,
            'closed' => $closed
        ]);

        $request->user()->update([
            "clan_id" => $clan->id
        ]);

        return response()->json(['response' => $clan], 200);
    }

    public function search(Request $request) {
        $response = $request->all();

        $search = isset($response['search']) ? $response['search'] : null;

        if(empty($search)) {
            return response()->json([
                'error' => [
                    'error_code' => 101,
                    'error_message' => 'Params not sended'
                ]
            ], 200);
        }

        $clans = Clan::where([['title','LIKE',"%".$search."%"]])->orderBy('id', 'DESC')->take(5)->get();
        return response()->json(['response' => $clans], 200);
    }
}
