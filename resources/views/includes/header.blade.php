<header>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('dashboard') }}">Home</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    
                    @if (Auth::user())
                        <li><a href={{ route('listUser')}}>List user</a></li>
                        <li><a href="{{ route('account') }}">{{ Auth::user()->first_name }}</a></li>

                        <li id="noti_Container">
                            {{-- unread notification --}}
                            <div id="noti_Counter" style="opacity: 1; top: 13px;">
                                @if ( App\Notification::where('user_id', Auth::user()->id)->where('seen', 0)->count() != 0)
                                    {{ App\Notification::where('user_id', Auth::user()->id)->where('seen', 0)->count() }}
                                @endif
                            </div>

                            <div id="noti_Button"></div>
                            <div id="notifications" style="display: none;">
                                <h3>Notifications</h3>
                                <ul style="height: 100%;">
                                @foreach(App\Notification::where('user_id', Auth::user()->id)
                                                            ->orderBy('created_at', 'desc')
                                                            ->take(10)
                                                            ->get() as $notification)
                                    <li style="text-decorator: none;"> 
                                        <div class="notification"> 
                                            <a href="{{ route('view-post', ['post_id' => $notification->post_id]) }}"> {{$notification->body}} </a>
                                        </div>
                                        <hr style="color: #333; margin-left: -40px;">
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
                    
                    <li><a href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</header>


<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

<script>
var token = '{{ Session::token() }}';

$('#noti_Button').click(function () {
    $('#notifications').fadeToggle('fast', 'linear', function () {
        if ($('#notifications').is(':hidden')) {
            $('#noti_Button').css('background-color', '#2E467C');
        } else $('#noti_Button').css('background-color', '#FFF');
    });
    $('#noti_Counter').fadeOut('slow');


    $.ajax({
            method: 'get',
            url: '/noticount/reset',
            dataType: "json",
            data: {
                _token: token
            }
        })
        .done(function (msg) {
            console.log(msg);
        })
        .error(function (err) {
            console.log(err);
        });
});
</script>