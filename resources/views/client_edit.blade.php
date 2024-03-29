@extends('site')
@section('header')
    <title>Media24 - Edit Client</title>
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
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <div class="viewer-title">
        <h2 class="heading">Update Client</h2>
        <div class="list-links">
            <a href="/client/delete/{{$client->id}}">Delete this client</a> | 
            <a href="/client/view/{{$client->id}}">View Client</a>
        </div>
    </div>
    <div>
        <form action="/client/update/{{$client->id}}" method="post">
        @csrf
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="name" value="{{$client->name}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Active Directory Username:</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="ad_user" value="{{$client->ad_user}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Assigned Device:</label>
                </div>
                <div class="device_form_row_input">
                    <select class="form-control" name="device_id">
                        <option value="0">No device allocated</option>
                        @foreach ($devices as $device)
                            <option value="{{$device->id}}" @if ($device->computername == $device_name) selected @endif>{{$device->computername}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="ww_user">Woodwing User?:</label>
                </div>
                <div class="device_form_row_input">
                    <select class="form-control" name="ww_user">
                        <option value="0">No</option>
                        <option value="1" @if ($client->ww_user == 1) selected @endif>Yes</option>
                    </select>
                </div>
            </div>            
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="role">Comment:</label>
                </div>
                <div>
                    <textarea class="form-control" name="comment">{{$client->comments}}</textarea>
                </div>
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Update Client</button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection