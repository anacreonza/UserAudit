@extends('site')
@section('header')
    <title>User Viewer</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Users
    @endslot
    @endcomponent
@endsection
@section('content')
    <div class="item-container">
        <div class="item-view-left">
            <a href="/user/delete/{{$user->id}}">Delete this user</a>
        </div>
        <div class="item-view-right">
            <h1>User data</h1>
            <pre>{{var_dump($user)}}</pre>
        </div>
    </div>
    @endsection