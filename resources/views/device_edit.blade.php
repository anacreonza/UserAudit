@extends('site')
@section('header')
    <title>Media24 - Edit Device</title>
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
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="item-container">
    <div class="item-view-left">
        <p><a href="/device/delete/{{$device->id}}">Delete this device</a></p>
        <p><a href="/device/view/{{$device->id}}">View device</a></p>
        <p><a href="/device/get_me_details/{{$device->id}}">Get machine specs</a></p>
    </div>
    <div class="item-view-right">
        <h2 class="heading">Update {{$device->computername}}</h2>
        <form action="/device/update/{{$device->id}}" method="post">
        @csrf
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Assigned User:</label>
                </div>
                <select class="form-control" name="assigned_user_id">
                    <option value="0">No user allocated</option>
                    @foreach ($clients as $client)
                        <option value="{{$client->id}}" @if ($client->id == $device->assigned_user_id) selected @endif>{{$client->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_type">Device Type:</label>
                </div>
                <div class="device_form_row_input">
                    <select name="device_type" id="device_type" class="form-control">
                        @php
                        $types = ["None", "NoteBook", "Laptop", "Desktop", "Macbook Pro", "iMac", "Mac Mini"]
                        @endphp
                        @foreach ($types as $type)
                        <option value="{{$type}}" @if($device->device_type == $type) selected @endif>{{$type}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="operating_system">Operating System:</label>
                </div>
                <select name="operating_system" id="operating_system" class="form-control">
                    @php $operating_systems = [
                    "Unknown",
                    "Windows 10 Professional Edition (x64)",
                    "Windows 11 Professional Edition (x64)",
                    "macOS - Ventura",
                    "macOS - Monterey",
                    "macOS - Big Sur"]
                    @endphp
                    @foreach ($operating_systems as $system)
                    <option value="{{$system}}" @if ($device->operating_system == $system) selected @endif>{{$system}}</option>
                    @endforeach
                </select>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_manifest">Software Manifest:</label>
                </div>
                <select name="device_manifest" id="device_manifest" class="form-control">
                    @php $manifests = [
                        "None",
                        "Content Gatherer",
                        "Woodwing Designer",
                        "Woodwing Sub With InCopy",
                        "Woodwing Sub With InDesign",
                        "Woodwing Retoucher",
                        "Designer"
                        ]
                    @endphp
                    @foreach ($manifests as $manifest)
                    <option @if ($device->machine_manifest == $manifest) selected @endif value="{{$manifest}}">{{$manifest}}</option>
                    @endforeach
                </select>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="serial_no">Serial Number:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="serial_no" class="form-control" value="{{$device->serial_no}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_type">Device Model:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="device_model" class="form-control" value="{{$device->device_model}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="role">Comment:</label>
                </div>
                <div>
                    <textarea class="form-control" name="comments"></textarea>
                </div>
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Update Device</button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection