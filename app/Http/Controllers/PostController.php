<?php
namespace App\Http\Controllers;

use App\Like;
use App\Post;
use App\Comment;
use App\Follow;
use App\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class PostController extends Controller
{
    

    public function getDashboard()
    {
        //$posts = Post::orderBy('created_at', 'desc')->get();
        $user_id = Auth::user()->id;
        $followerPosts = DB::table('posts')
                ->join('follows', function($join) {
                    $join->on('posts.user_id',  '=', 'follows.following_id')
                    ->where('follows.follower_id', '=', Auth::user()->id);
                })
                ->select('posts.*')
                ->orderBy('created_at', 'desc')
                ->get();
        
        //dd($followerPosts);

        $ownPost = DB::table('posts')
                    ->where('posts.user_id', '=', Auth::user()->id)
                    ->get();


        $posts = array_merge($followerPosts, $ownPost);
        

        $comments = DB::select('select * from comments order by if(parent_id = 0, id, parent_id)');

        //$notifications = $this->getNotification();

        return view('dashboard', ['posts' => $posts, 'comments' => $comments]);
    }

    public function postCreatePost(Request $request)
    {
        $this->validate($request, [
            'body' => 'required|max:1000'
        ]);
        $post = new Post();
        $post->body = $request['body'];
        $message = 'There was an error';
        if ($request->user()->posts()->save($post)) {
            $message = 'Post successfully created!';
        }
        return redirect()->route('dashboard')->with(['message' => $message]);
    }

    public function getDeletePost($post_id)
    {
        $post = Post::where('id', $post_id)->first();
        if (Auth::user() != $post->user) {
            return redirect()->back();
        }
        $post->delete();
        return redirect()->route('dashboard')->with(['message' => 'Successfully deleted!']);
    }

    public function postEditPost(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ]);
        $post = Post::find($request['postId']);
        if (Auth::user() != $post->user) {
            return redirect()->back();
        }
        $post->body = $request['body'];
        $post->update();
        return response()->json(['new_body' => $post->body], 200);
    }

    public function postLikePost(Request $request)
    {
        $post_id = $request['postId'];
        $is_like = $request['isLike'] === 'true';
        $update = false;
        $post = Post::find($post_id);
        if (!$post) {
            return null;
        }
        $user = Auth::user();
        $like = $user->likes()->where('post_id', $post_id)->first();
        if ($like) {
            $already_like = $like->like;
            $update = true;
            if ($already_like == $is_like) {
                $like->delete();
                return null;
            }
        } else {
            $like = new Like();
        }
        $like->like = $is_like;
        $like->user_id = $user->id;
        $like->post_id = $post->id;
        if ($update) {
            $like->update();
        } else {
            $like->save();
        }
        return null;
    }
}