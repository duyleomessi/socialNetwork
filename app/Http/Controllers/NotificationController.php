<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

use App\Notification;

class NotificationController extends Controller
{
    public function viewNotification(Request $request, $post_id) {
        $post = Post::find($post_id);
        $comments = DB::select('select * from comments order by if(parent_id = 0, id, parent_id)');
        return view('detail', ['post' => $post, 'comments' => $comments]);
    }

    public function updateNotificationCount(Request $request) 
    {
        $user_id = Auth::user()->id;
        error_log($user_id);

        $affectedRows = Notification::where('user_id', $user_id)->where('seen', 0)->update(['seen' => 1]);
        return response()->json(["message" => $affectedRows], 200);

    }
}
