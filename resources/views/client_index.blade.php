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
    <div class="container">
        <div class="viewer-title">
            <div>
                <h2>Clients <span class="badge bg-info"> {{$clientlist->total()}} </span></h2>
            </div>
            <div class="list-links">
                <div class="list-link-item">
                    <a href="/client/create" role="button">Add a new client</a> |
                    <a href="/client/export/csv">Export CSV</a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-sm w-auto">
                <thead class="table-light">
                    <tr>
                        <th scope="col"><a href="/client/index/?sortby=name">Name</a></th>
                        <th scope="col"><a href="/client/index/?sortby=department">Department</a></th>
                        <th scope="col"><a href="/client/index/?sortby=device_id">Assigned Computer</a></th>
                        <th scope="col"><a href="/client/index/?sortby=role">Role</a></th>
                        <th scope="col"><a href="/client/index/?sortby=ww_user">Woodwing User?</a></th>
                        <th scope="col"><a href="/client/index/?sortby=updated_at">Last Activity Date</a></th>
                        <th scope="col"><a href="/client/index/?sortby=last_activity">Last Activity</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientlist as $client)
                    <tr>
                        <td><a href="/client/view/{{$client->ad_user}}" class="small text-nowrap">{{$client->name}}</a></td>
                        <td><p class="text-muted small text-nowrap">{{$client->department}}</p></td>
                        @if ($client->device_id !== "0")
                        <td><a href="/device/view/{{$client->computername}}" class="small text-nowrap">{{$client->computername}}</a></td>
                        @else
                        <td><p class="text-muted small text-nowrap">{{$client->computername}}</p></td>
                        @endif
                        <td><p class="text-muted small">{{$client->role}}</p></td>
                        @if ($client->ww_user == 1)
                        <td><p class="text-muted small">Yes</p></td>
                        @else
                        <td><p class="text-muted small">No</p></td>
                        @endif
                        <td><p class="text-muted small text-nowrap">{{$client->updated_at}}</p></td>
                        <td><p class="text-muted small">{{$client->journal_entry_preview}}</p></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{$clientlist->links()}}
        </div>
    </div>
@endsection