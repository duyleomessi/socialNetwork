@extends('layouts.master')

@section('title')
    Account
@endsection

@section('content')
    <section class="row new-post">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>Your Account</h3></header>
            <form action="{{ route('account.save') }}" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" id="first_name">
                </div>
                <div class="form-group">
                    <label for="image">Image (only .jpg)</label>
                    <input type="file" name="image" class="form-control" id="image">
                </div>
                @if (Auth::user() == $user)
                    <button type="submit" class="btn btn-primary">Save Account</button>
                @endif
                <input type="hidden" value="{{ Session::token() }}" name="_token" id="_token">
            </form>

            <form>
                <input type="hidden" value="{{ Session::token() }}" name="_token">
                <input type="hidden" value="{{ Auth::user()->id }}" name="follower_id" id="follower_id">
                <input type="hidden" value="{{ $user->id }}" name="following_id" id="following_id">
                @if (Auth::user() != $user)
                    <input type="submit" 
                    value= {{ App\Follow::where('following_id', $user->id)->where('follower_id', Auth::user()->id)->first() 
                    ? "Following" : "Follow"}} 
                    class="btn btn-primary" id="follow_button">
                @endif
            </form>
            
        </div>
    </section>
    @if (Storage::disk('local')->has($user->first_name . '-' . $user->id . '.jpg'))
        <section class="row new-post">
            <div class="col-md-6 col-md-offset-3">
                <img src="{{ route('account.image', ['filename' => $user->first_name . '-' . $user->id . '.jpg']) }}" alt="" class="img-responsive">
            </div>
        </section>
    @endif

<script>
    var token = '{{ Session::token() }}';
    var urlFollow = '{{ route('addfollow') }}';
    var urlDelete = '{{ route('deletefollow')}}'
</script>
@endsection