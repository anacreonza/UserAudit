@extends('site')
@section('header')
    <title>Client Viewer</title>
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
    <div class="item-container">
        <div class="item-view-left">
            <p><a href="/client/edit/{{$client->id}}">Edit user</a></p>
            <p><a href="/client/lookup/{{$client->id}}">Get User details from LDAP</a></p>
            <p><a href="/client/delete/{{$client->id}}">Delete user</a></p>
        </div>
        <div class="item-view-right">
            <h1>{{$client->name}}</h1>
            <ul>
                <li>Active Directory Username: {{$client->ad_user}}</li>
                <li>Email: <a href="mailto:{{$client->email}}">{{$client->email}}</a></li>
                <li>Department: {{$client->department}}</li>
                <li>Role: {{$client->role}}</li>
                @if ($client->manager)
                <li>Manager: {{$client->manager}}</li>
                @endif
                @if ($client->mobile)
                <li>Contact No: {{$client->mobile}}</li>
                @endif
                @if ($device->computername == 'None')
                <li>Assigned Device: None <a href="/device/create/{{$client->id}}">Create a device</a></li>
                @else
                <li>Assigned Device: <a href="/device/view/{{$device->id}}">{{$device->computername}}</a></li>
                <ul>
                    <li>Serial Number: {{$device->serial_no}}</li>
                    <li>Device Model: {{$device->device_model}}</li>
                    <li>Operating System: {{$device->operating_system}}</li>
                </ul>
                @endif
                @if ($client->ww_user == 1)
                <li>User is a Woodwing User</li>
                @endif
                <li>Comment: {{$client->comments}}</li>
            </ul>
            <div>
                <h2>Activity:</h2>
                <a href="/journal_entry/create/{{$client->id}}">Add new journal entry</a>
                <div>
                    <div class="user_journal_list_grid_row">
                        <div class="journal_list_grid_item">Date</div>
                        <div class="journal_list_grid_item">Entry details</div>
                    </div>
                    @if ($journal_entries)
                        @foreach ($journal_entries as $journal_entry)
                            <div class="user_journal_list_grid_row">
                                <div class="journal_list_grid_item">{{$journal_entry->updated_at}}</div>
                                <div class="journal_list_grid_item">{{$journal_entry->journal_entry}}</div>
                                <div class="journal_list_grid_item">
                                    <form method="post" action="/journal_entry/delete/{{$journal_entry->id}}"> 
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">delete</button>
                                    </form>

                                </div>
                            </div>    
                        @endforeach
                    @else
                        <div>No journal entries found</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endsection