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
        <div class="index-header">
            <div>
                <h2>Clients <span class="badge badge-info"> {{$clientcount}} </span></h2>
            </div>
            <div class="list-links">
                <form action="">
                    <label for="pagination" class="form-label">Items per page</label>
                    <select name="pagination" id="pagination" class="form-select">
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="all">All</option>
                    </select>
                </form>
                <div>
                    <a href="/client/create" role="button">Add a new client</a>
                    |
                    <a href="/client/export/csv">Export CSV</a>
                </div>
            </div>
        </div>
        <div class="user_list_grid">
            <div class="user_list_grid_row">
                <div class="user_list_grid_item"><a href="/client/index/?sortby=name">Name</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=department">Department</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=device_id">Assigned Computer</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=role">Role</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=ww_user">Woodwing User?</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=updated_at">Last Activity Date</a></div>
                <div class="user_list_grid_item"><a href="/client/index/?sortby=last_activity">Last Activity</a></div>
            </div>
            @foreach ($clientlist as $client)
            <div class="user_list_grid_row">
                <div class="user_list_grid_item"><a href="/client/view/{{$client->ad_user}}">{{$client->name}}</a></div>
                <div class="user_list_grid_item">{{$client->department}}</div>
                @if ($client->device_id !== "0")
                <div class="user_list_grid_item"><a href="/device/view/{{$client->assigned_device_name}}">{{$client->assigned_device_name}}</a></div>
                @else
                <div class="user_list_grid_item">{{$client->assigned_device_name}}</div>
                @endif
                <div class="user_list_grid_item">{{$client->role}}</div>
                @if ($client->ww_user == 1)
                <div class="user_list_grid_item">Yes</div>
                @else
                <div class="user_list_grid_item">No</div>
                @endif
                <div class="user_list_grid_item">{{$client->updated_at}}</div>
                <div class="user_list_grid_item">{{$client->journal_entry_preview}}</div>
            </div>
            @endforeach
        </div>
    </div>
@endsection