@extends('site')
@section('header')
    <title>Media24 - Users</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Clients
    @endslot
    @endcomponent
@endsection
@section('content')
    @if (session('message'))
    <div class="alert alert-warning">
        {{ session('message') }}
    </div>
    @endif
    <div class="container-fluid">
        <div class="heading">
            <div>
                <h2>Clients <span class="badge badge-info"> {{$clientcount}} </span></h2>
            </div>
            <div>
                <a class="btn btn-primary" href="/client/create" role="button">New client</a>
            </div>
        </div>
        <div class="user_list_grid">
            <div class="user_list_grid_row">
                <div class="user_list_grid_item"><a href="/client/index/?sortby=name">Name</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=department">Department</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=role">Role</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=updated_at">Last Activity Date</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=last_activity">Last Activity</a></div>
            </div>
            @foreach ($clientlist as $client)
            <div class="user_list_grid_row">
                <div class="user_list_grid_item"><a href="/client/view/{{$client->id}}">{{$client->name}}</a></div>
                <div class="user_list_grid_item">{{$client->department}}</div>
                <div class="user_list_grid_item">{{$client->role}}</div>
                <div class="user_list_grid_item">{{$client->updated_at}}</div>
                <div class="user_list_grid_item">{{$client->last_journal_entry["journal_entry"]}}</div>
            </div>
            @endforeach
        </div>
    </div>
@endsection