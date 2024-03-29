@extends('site')
@section('header')
    <title>Media24 - New User</title>
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
        <h2 class="heading">New Client</h2>
    </div>
    <div class="device_form_container">
        <form action="/client/store" method="post">
            @csrf
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="ad_user">Active Directory User Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="ad_user" value="{{ old('ad_user') }}">
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
                        <option value="{{$device->id}}">{{$device->computername}}</option>
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
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="role">Comment:</label>
                </div>
                <div>
                    <textarea class="form-control" type="textarea" name="comment" value="{{ old('comments')}}"></textarea>
                </div>
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Create Client</button>
            </div>
        </form>
    </div>
</div>
@endsection