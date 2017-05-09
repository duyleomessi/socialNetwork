@extends('layouts.master')

@section('content')
    @include('includes.message-block')
    <section class="row new-post">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>What do you have to say?</h3></header>
            <form action="{{ route('post.create') }}" method="post">
                <div class="form-group">
                    <textarea class="form-control" name="body" id="new-post" rows="5"
                              placeholder="Your Post"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Post</button>
                <input type="hidden" value="{{ Session::token() }}" name="_token">
            </form>
        </div>
    </section>
    <section class="row posts">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>What other people say...</h3></header>
            @foreach($posts as $post)
                <article class="post" data-postid="{{ $post->id }}">
                    <p>{{ $post->body }}</p>
                    <div class="info">
                        Posted by {{ $post->user->first_name }} on {{ $post->created_at }}
                    </div>
                    <div class="interaction">
                        <a href="#"
                           class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 1 ? 'You like this post' : 'Like' : 'Like'  }}</a> |
                        <a href="#"
                           class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 0 ? 'You don\'t like this post' : 'Dislike' : 'Dislike'  }}</a> |
                        <a href="#"
                           class="comment" role="button">{{ 'Comment' }}</a>
                        @if(Auth::user() == $post->user)
                            |
                            <a href="#" class="edit">Edit</a> |
                            <a href="{{ route('post.delete', ['post_id' => $post->id]) }}">Delete</a>
                        @endif

                        <ul >
                        @foreach($comments as $comment)
                            <li class="comment-section">
                            @if($comment->post_id == $post->id)
                                @if ($comment->parent_id == 0)
                                    <div>
                                        <div> {{$comment->body}} <div>
                                        <div> Post by {{ $post->user->first_name }} on {{ $comment->created_at }} <div>
                                        
                                        <div class="interaction">
                                            <a href="#" class="reply">Reply</a>
                                        </div>
                                        <br> 
                                    </div>
                                @else 
                                    <div style="margin-left: 40px;">                                    
                                        <div> {{$comment->body}} <div>
                                        <div> Post by {{ $post->user->first_name }} on {{ $comment->created_at }} <div>
                                        <br>
                                    </div>
                                    <form action="{{ route('reply', ['parent_id' => $comment->parent_id]) }}" method="post" class="reply-form">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="body" id="commentBody">
                                        </div>
                                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                                        <input type="hidden" name="post_id" value={{ $post->id }}>
                                        <button type="submit" class="btn btn-primary hide">Submit</button>
                                    </form>
                                @endif
                            @endif
                            </li>
                        @endforeach
                        </ul>

                        <form action="{{ route('newComment', ['post_id' => $post->id]) }}" method="post" class="show" id="comment-form">
                            <div class="form-group">
                                <input class="form-control" type="text" name="body" id="commentBody">
                            </div>
                            <input type="hidden" name="_token" value="{{ Session::token() }}">
                            <button type="submit" class="btn btn-primary hide">Submit</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Post</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="post-body">Edit the Post</label>
                            <textarea class="form-control" name="post-body" id="post-body" rows="5"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-save">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        var token = '{{ Session::token() }}';
        var urlEdit = '{{ route('edit') }}';
        var urlLike = '{{ route('like') }}';
    </script>
@endsection