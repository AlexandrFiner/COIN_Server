<?php

namespace App\Http\Controllers;

use App\Models\Clan;
use Illuminate\Http\Request;

class ClanController extends Controller
{
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
        $closed = isset($response['closed']) ? (bool)$response['closed'] : null;

        if(empty($title) || empty($description) || empty($closed)) {
            return response()->json([
                'error' => [
                    'error_code' => 102,
                    'error_message' => 'Params not sended'
                ]
            ], 200);
        }

        $clan = Clan::create([
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

        $clans = Clan::where([['title','LIKE',"%".$search."%"]])->orderBy('id', 'DESC')->get();
        return response()->json(['response' => $clans], 200);
    }
}
