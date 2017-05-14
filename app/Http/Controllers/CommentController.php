<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Post;
use App\User;
use App\Comment;
use App\Follow;


class CommentController extends Controller
{
    public function store(Request $request, $post_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);

        $comment = new Comment();
        $comment->body = $request['body'];
        $comment->post_id = $post_id;

        $request->user()->posts()->save($comment);
        
        return redirect()->route('dashboard');

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
