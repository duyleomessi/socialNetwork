@extends('layouts.master')

@section('content')
<h1>Listing User</h1>

<ul>
    @foreach($users as $user)
        <li> <a href="{{ route('user-profile', ['id' => $user->id]) }}"> {{ $user-> first_name }}</a> </li>
    @endforeach
</ul>
@endsection