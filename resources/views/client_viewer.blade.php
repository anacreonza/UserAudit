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
                <div>
                    <a href="/client/edit/{{$client->id}}">Edit user</a> |
                    <a href="/client/delete/{{$client->id}}" onclick="return confirm('Are you sure you wish to delete this user?')">Delete user</a>
                    @if (! $client->device_id)
                    | <a href="/device/find_in_me/{{$client->ad_user}}">Find device in Manage Engine</a>
                    @else
                    | <a href="/device/find_in_me/{{$client->ad_user}}">Update device details from Manage Engine</a>
                    @endif
                </div>
            </div>
            @else
            <div class="list-links">
                <p><a href="/client/add_client/{{$client->ad_user}}">Add client to managed clients list</a></p>
            </div>
            @endif
        </div>
        <div>
            <h2>Client Details</h2>
            <hr>
            <div class="details-container">
                <div class="details-box" id="personal_details">
                    <p><b>Username:</b> {{$client->ad_user}}</p>
                    <p><b>Primary Email:</b> <a href="mailto:{{$client->email}}">{{$client->email}}</a></p>
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
                        @if (isset($client->physicaladdress)){{$client->physicaladdress}}<br> @endif
                        @if (isset($client->streetaddress)){{$client->streetaddress}}<br> @endif
                        @if (isset($client->location)){{$client->location}}<br> @endif
                        @if (isset($client->country)){{$client->country}}<br> @endif
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
                @if(isset($client->aliases))
                <div class="details-box">
                    <p class="small"><b>Email Aliases:</b></p>
                    @foreach ($client->aliases as $alias)
                        <p class="small">{{$alias}}</p>
                    @endforeach
                </div>
                @endif
                @if(isset($client->lockouttime))
                <div class="details-box">
                    <p><b>Lock Out Time: </b>{{$client->lockouttime}}</p>
                </div>
                @endif
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
                @if (isset($client->id))
                <h3>Activity:</h3>
                <hr>
                <a href="/journal_entry/create/{{$client->id}}">Add new journal entry</a>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <th scope="col">Date</th>
                            <th scope="col">Entry Details</th>
                        </thead>
                        <tbody>
                        @if(isset($journal_entries))
                        @foreach($journal_entries as $journal_entry)
                            <tr>
                                <td><p class="small">{{$journal_entry->updated_at}}</p></td>
                                <td><p class="small">{{$journal_entry->journal_entry}}</p></td>
                                @if(isset($journal_entry->attachment))
                                <td><a href="/download/{{$journal_entry->attachment}}" alt="alt-text" class="small">download attachment</a></td>
                                @endif
                                <td>
                                    <form method="post" action="/journal_entry/delete/{{$journal_entry->id}}"> 
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure you wish to delete this journal entry?')">delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td>No journal entries found</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endsection