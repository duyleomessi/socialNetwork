<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

use Log;
use App\Post;
use App\User;
use App\Comment;
use App\Follow;


class CommentController extends Controller
{
    public function store(Request $request)
    {
       
        $comment = new Comment();
        $comment->body = Input::get('body');
        $comment->post_id = Input::get('post_id');
        // save comment
        $request->user()->posts()->save($comment);
        // user that post comment
        $username = Auth::user()->first_name;
        $comment->username = $username;

        $postBy = Post::find($comment->post_id)->user_id;
        if($postBy != Auth::user()->id) {
            // create notification
            $textNotification = $username . " comment on your post";
            
            // get pusher instance from service container
            $pusher = App::make('pusher');

            $data = array('text' => $textNotification, 'postBy' => $postBy);

            // trigger the event
            $pusher->trigger('comment-notification', 'comment-event', $data);
            
        }
        

        //return redirect()->route('dashboard');
        return response()->json(["message" => $comment], 200);

    }

    public function reply(Request $request, $parent_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        $comment = new Comment();
        $comment->body = $request['body'];
        $comment->post_id = $request['post_id'];
        $comment->parent_id = $parent_id;
 
        $request->user()->posts()->save($comment);
        
        return redirect()->route('dashboard');

    }

}
