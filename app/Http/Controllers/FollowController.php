<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

// controller
use App\Post;
use App\User;
use App\Comment;
use App\Follow;

class FollowController extends Controller
{
    public function addFollow(Request $request)
    {
        $follow = new Follow();
        $follow->follower_id = $request['follower_id'];
        $follow->following_id = $request['following_id'];

        error_log($follow);

        if ($follow->save()) {
            error_log("success");
            return response()->json(["message" => "Success"], 200);
        } else {
            error_log("fail");
            return response()->json(["message" => "fail"], 400);
        }
    }

    public function deleteFollow(Request $request) {
        error_log('do it');

        $follower_id = $request['follower_id'];
        $following_id = $request['following_id'];
        error_log($follower_id);
        error_log($following_id);
        $follow = Follow::where('follower_id', $follower_id)->where('following_id', $following_id)->first();

        error_log($follow);

        if ( $follow->delete() ) {
            error_log("success");
            return response()->json(["message" => "Success"], 200);
        } else {
            error_log("fail");
            return response()->json(["message" => "fail"], 400);
        }
    }

}
