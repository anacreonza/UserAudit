@extends('site')
@section('header')
    <title>Media24 - Users</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Users
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
            <h2>Users</h2>
            <button onclick="document.location='/user/create'">Add a new user</button>
        </div>
        <div class="table_container">
            <table class="item_table">
                <tr>
                    <th>User Name</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Last Activity Date</th>
                    <th>Last Journal Entry</th>
                </tr>
                @foreach ($userlist as $user)
                <tr>
                    <td><a href="/user/view/{{$user->id}}">{{$user->name}}</a></td>
                    <td>{{$user->department}}</td>
                    <td>{{$user->role}}</td>
                    <td>{{$user->updated_at}}</td>
                    <td>{{$user->last_journal_entry}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection