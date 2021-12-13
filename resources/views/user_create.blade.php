@extends('site')
@section('header')
    <title>Media24 - New User</title>
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
    <h2 class="heading">New User</h2>
    <form action="/user/store" method="get">
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">User Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="name">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="email">EMail:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="email">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Assigned Device:</label>
                </div>
                <div class="device_form_row_input">
                    <select name="device_id">
                        <option value="0">No device allocated</option>
                        @foreach ($devices as $device)
                            <option value="{{$device->id}}">{{$device->computername}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Department:</label>
                </div>
                <div>
                    <input type="text" name="department">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="username">Role:</label>
                </div>
                <div>
                    <input type="text" name="role">
                </div>
            </div>
            <div>
                <input type="submit" value="Create User">
            </div>
        </div>
    </form>
</div>
@endsection