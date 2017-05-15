@extends('layouts.master')

@section('content')
    <section class="row posts">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>What other people say...</h3></header>
                <input type="hidden" id="ownId" value="{{ Auth::user()->id }}">
                <article class="post" data-postid="{{ $post->id }}">
                    <p>{{ $post->body }}</p>
                    <div class="info">
                        Posted by <a href="{{ route('user-profile', ['id' => $post->user_id]) }}" >  {{ App\User::find($post->user_id)->first_name }}  </a> on {{ $post->created_at }}
                    </div>
                    <div class="interaction">
                        <a href="#"
                           class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 1 ? 'You like this post' : 'Like' : 'Like'  }}</a> |
                        <a href="#"
                           class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 0 ? 'You don\'t like this post' : 'Dislike' : 'Dislike'  }}</a> |
                        <a href="#"
                           class="comment" role="button">{{ 'Comment' }}</a>
            
                        @if(Auth::user()->id == $post->user_id)
                            |
                            <a href="#" class="edit">Edit</a> |
                            <a href="{{ route('post.delete', ['post_id' => $post->id]) }}">Delete</a>
                        @endif

                        <ul >
                        @foreach($comments as $comment)
                            <li class="comment-section-{{$post->id}}">
                            @if($comment->post_id == $post->id)

                                {{-- check if comment is parent comment or reply comment --}}
                                @if ($comment->parent_id == 0)
                                    <div class="parent-comment">
                                        <div> {{$comment->body}} <div>
                                        <div> Reply by <a href="{{ route('user-profile', ['id' => $comment->user_id]) }}" > {{ App\User::find($comment->user_id) ->first_name }} </a> on {{ $comment->updated_at }} <div>
                                        <br> 
                                    </div>
                                @else 
                                    {{-- if it is reply comment --}}
                                    <div style="margin-left: 40px;">                                    
                                        <div> {{$comment->body}} <div>
                                        <div> Reply by <a href="{{ route('user-profile', ['id' => $comment->user_id]) }}" > {{ App\User::find($comment->user_id)->first_name }} </a> on {{ $comment->updated_at }} <div>
                                        
                                        <br>
                                    </div>
                                    <form action="{{ route('reply', ['parent_id' => $comment->parent_id]) }}" method="post" class="reply-form">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="body" id="commentBody">
                                        </div>
                                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                                        <input type="hidden" name="post_id" id="post_id" value={{ $post->id }}>
                                        <button type="submit" class="btn btn-primary hide">Submit</button>
                                    </form>
                                @endif
                            @endif
                            </li>
                        @endforeach
                        </ul>

                        {{-- Comment section --}}
                        <form class="comment-form">
                            <div class="form-group">
                                <input class="form-control" type="text" name="body" class="commentBody" value=''>
                                <input type="hidden" class="post_id" id="post_id" value="{{ $post->id }}">
                                <button type="submit" value="submit" class="btn btn-primary hide" id="commentSubmitButton"></button>
                            </div>
                        </form>
                    </div>
                </article>
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

     <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="//js.pusher.com/3.0/pusher.min.js"></script>

    <script>
        var token = '{{ Session::token() }}';
        var urlEdit = '{{ route('edit') }}';
        var urlLike = '{{ route('like') }}';

        
        // Ensure CSRF token is sent with AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Added Pusher logging
        Pusher.log = function(msg) {
            console.log(msg);
        };

        //submit comment
        $('.comment-form').each(function(index) {
            $(this).on('submit' ,function(e) {
                e.preventDefault();
                var commentBody = $(this).children(".commentBody")['context'][0].value;
                var post_id = $(this).children(".post_id")['context'][1].value;

                $.ajax({
                    method: 'post',
                    url: '/comments/post',
                    data: {
                        body: commentBody,
                        post_id: post_id,
                        _token: token
                    }
                })
                .done(function(data) {
                    console.log("data: ", data);
                    var comment = data.message;
                    
                    //var classname = ".parent-comment-" + comment.post_id;
                    var classname = ".comment-section-" + comment.post_id + ":last-child";
                    $(classname)
                    .append('<div class="parent-comment">' 
                    + '<div>' + comment.body + '</div>' 
                    + '<div>' + ' Reply by ' + comment.username  
                    + ' on ' + comment.updated_at + '</div>'    
                    + '</div>' );

                })
                .error(function(err) {
                    console.log('error: ', err);
                })
            })
        });

        function showNotification(data) {
            // get text from event data
            var text = data.text;
            // use the text in the  notification
            toastr.success(text, null, {"positionClass": "toast-bottom-left"});
        }

        var ownId = $('#ownId').val();
        console.log('ownId', ownId);
        var pusher = new Pusher('{{env("PUSHER_KEY")}}');
        var channel = pusher.subscribe('comment-notification');
        channel.bind('comment-event', function(data) {
            console.log("data is: ", data);
            if(ownId == data.postBy) {
                showNotification(data);
            }
        }); 
       
    </script>
@endsection