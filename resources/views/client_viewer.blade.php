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
    <div class="container">
        <div class="viewer-title">
            <h1>{{$client->name}}</h1>
            @if (isset($client->id))
            <div class="list-links">
                <a href="/client/edit/{{$client->id}}">Edit user</a> |
                <a href="/client/delete/{{$client->id}}" onclick="return confirm('Are you sure you wish to delete this user?')">Delete user</a>
                @if (! $client->device_id)
                | <a href="/device/find_in_me/{{$client->ad_user}}">Find device in Manage Engine</a>
                @endif
            </div>
            @else
            <div class="list-links">
                <p><a href="/client/create/">Add client to managed clients list</a></p>
            </div>
            @endif
        </div>
        <div>
            <h2>Client Details</h2>
            <hr>
            <div class="details-container">
                <div class="details-box" id="personal_details">
                    <p><b>Username:</b> {{$client->ad_user}}</p>
                    <p><b>Email:</b> <a href="mailto:{{$client->email}}">{{$client->email}}</a></p>
                    @if (isset($client->role))
                    <p><b>Role:</b> {{$client->role}}</p>
                    @endif
                    @if ($client->manager)
                        @if ($client->manager_id)
                        <p><b>Manager:</b> <a href="/client/view/{{$client->manager_ad_username}}">{{$client->manager}}</a></p>
                        @else
                        <p><b>Manager:</b> {{$client->manager}}</p>
                        @endif
                    @endif
                    @if ($client->ww_user == 1)
                    <p><b>User is a Woodwing User</b></p>
                    @endif
                </div>
                <div class="details-box" id="addresses">
                    @if ($client->physicaladdress)
                    <p><b>Physical Address:</b><br>
                        {{$client->company}}<br>
                        {{$client->physicaladdress}}<br>
                        {{$client->streetaddress}}<br>
                        {{$client->location}}<br>
                        {{$client->country}}<br>
                    </p>
                    @endif
                    @if ($client->mobile)
                    <p><b>Contact No:</b> {{$client->mobile}}</p>
                    @endif
                </div>
                <div class="details-box" id="device_details">
                    @if ($device->computername == 'None')
                    <p><b>Assigned Device:</b> None   </p>
                    @else
                    <p><b>Assigned Device:</b> <a href="/device/view/{{$device->computername}}" id="copytext">{{$device->computername}}</a> <a href="#" onclick="copyToClipboard()">Copy</a></p>
                    <p><b>Device Serial Number:</b> {{$device->serial_no}}</p>
                    <p><b>Device Model:</b> {{$device->device_model}}</p>
                    <p><b>OS:</b> {{$device->operating_system}}</p>
                    @endif
                </div>
                <div class="details-box"><p><b>Email Aliases:<br></b>
                    @foreach ($client->aliases as $alias)
                        {{$alias}}<br>
                    @endforeach
                    </p>
                    @if($client->lockouttime)
                    <p><b>Lock Out Time: </b>{{$client->lockouttime}}</p>
                    @endif
                </div>
                <div class="details-box">
                    <p>
                        @if ($client->directreports)
                        <b>Direct Reports:</b><br>
                        @foreach ($client->directreports as $report)
                            {{$report}}<br>
                        @endforeach
                        @endif
                    </p>
                </div>
                @if ($client->comments)
                <div class="details-box"><p><b>Comment:</b> {{$client->comments}}</p></div>
                @endif
            </div>
            <div>
                <h2>Activity:</h2>
                <hr>
                @if (isset($client->id))
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
                                <div>
                                    <div class="journal_list_grid_item">{{$journal_entry->journal_entry}}</div>
                                    @if(isset($journal_entry->attachment))
                                    <div class="journal_list_grid_item">• <a href="/download/{{$journal_entry->attachment}}" alt="alt-text">download attachment</a></div>
                                    @endif
                                </div>
                                <div class="journal_list_grid_item">
                                    <form method="post" action="/journal_entry/delete/{{$journal_entry->id}}"> 
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you wish to delete this journal entry?')">delete</button>
                                    </form>

                                </div>
                            </div>    
                        @endforeach
                    @else
                        <div>No journal entries found</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection