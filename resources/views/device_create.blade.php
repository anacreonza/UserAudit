@extends('site')
@section('header')
    <title>Media24 - Create a device</title>
@endsection

@section('navbar')
    @component('navbar')
    @slot('activetab')
        Devices
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
    <h2 class="heading">New Device</h2>
    <form action="/device/store" method="post">
    @csrf
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="computername">Device Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="computername" class="form-control">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="serial_no">Serial Number:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="serial_no" class="form-control">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Assigned User:</label>
                </div>
                <select class="form-control" name="assigned_user_id">
                    <option value="0">No user allocated</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_type">Device Type:</label>
                </div>
                <div class="device_form_row_input">
                    <select name="device_type" id="device_type" class="form-control">
                        <option value="None">None</option>
                        <option value="Windows Laptop">Windows Laptop</option>
                        <option value="Windows Desktop">Windows Desktop</option>
                        <option value="Macbook Pro">Macbook Pro</option>
                        <option value="iMac">iMac</option>
                        <option value="Mac Mini">Mac Mini</option>
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_type">Device Model:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="device_model" class="form-control">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="operating_system">Operating System:</label>
                </div>
                <select name="operating_system" id="operating_system" class="form-control">
                    <option value="Unknown">Unknown</option>
                    <option value="Windows 10 Professional Edition (x64)">Windows 10 Professional Edition (x64)</option>
                    <option value="Windows 11">Windows 11</option>
                    <option value="Mac OS 10.13 High Sierra">Mac OS 10.13 High Sierra</option>
                    <option value="Mac OS 10.14 Mojave">Mac OS 10.14 Mojave</option>
                    <option value="Mac OS 10.15 Catalina">Mac OS 10.15 Catalina</option>
                    <option value="Mac OS 11 Big Sur">Mac OS 11 Big Sur</option>
                    <option value="Mac OS 12 Monterey">Mac OS 12 Monterey</option>
                    <option value="Mac OS 13 Ventura">Mac OS 13 Ventura</option>
                </select>
            </div>
        </div>
        <div>
            <input type="submit" value="Create Device">
        </div>
    </form>
</div>
@endsection