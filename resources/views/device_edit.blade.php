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
                    <label for="device_type">Device type:</label>
                </div>
                <select name="device_type" id="device_type" class="form-control">
                    <option @if ($device->device_type == "None") selected @endif value="None">None</option>
                    <option @if ($device->device_type == "Windows Laptop") selected @endif value="Windows Laptop">Windows Laptop</option>
                    <option @if ($device->device_type == "Windows Desktop") selected @endif value="Windows Desktop">Windows Desktop</option>
                    <option @if ($device->device_type == "Macbook Pro") selected @endif value="Macbook Pro">Macbook Pro</option>
                    <option @if ($device->device_type == "iMac") selected @endif value="iMac">iMac</option>
                    <option @if ($device->device_type == "Mac Mini") selected @endif value="Mac Mini">Mac Mini</option>
                </select>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_model">Device Model:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" class="form-control" name="device_model" value="{{$device->device_model}}" autocomplete="on">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="serial_no">Serial Number:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" class="form-control" name="serial_no" value="{{$device->serial_no}}" autocomplete="on">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="operating_system">Operating System:</label>
                </div>
                <select name="operating_system" id="operating_system" class="form-control">
                    <option value="Unknown" @if ($device->operating_system == "Unknown") selected @endif>Unknown</option>
                    <option value="Windows 10 Professional Edition (x64)" @if ($device->operating_system == "Windows 10 Professional Edition (x64)") selected @endif>Windows 10 Professional Edition (x64)</option>
                    <option value="Windows 11" @if ($device->operating_system == "Windows 11") selected @endif>Windows 11</option>
                    <option value="Mac OS 10.13 High Sierra" @if ($device->operating_system == "Mac OS 10.13 High Sierra") selected @endif>Mac OS 10.13 High Sierra</option>
                    <option value="Mac OS 10.14 Mojave" @if ($device->operating_system == "Mac OS 10.14 Mojave") selected @endif>Mac OS 10.14 Mojave</option>
                    <option value="Mac OS 10.15 Catalina" @if ($device->operating_system == "Mac OS 10.15 Catalina") selected @endif>Mac OS 10.15 Catalina</option>
                    <option value="Mac OS 11 Big Sur" @if ($device->operating_system == "Mac OS 11 Big Sur") selected @endif>Mac OS 11 Big Sur</option>
                    <option value="Mac OS 12 Monterey" @if ($device->operating_system == "Mac OS 12 Monterey") selected @endif>Mac OS 12 Monterey</option>
                    <option value="Mac OS 13 Ventura" @if ($device->operating_system == "Mac OS 13 Ventura") selected @endif>Mac OS 13 Ventura</option>
                </select>
                
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="device_manifest">Software Manifest:</label>
                </div>
                <select name="device_manifest" id="device_manifest" class="form-control">
                    <option @if ($device->machine_manifest == "None") selected @endif value="None">None</option>
                    <option @if ($device->machine_manifest == "Content Gatherer") selected @endif value="Content Gatherer">Content Gatherer</option>
                    <option @if ($device->machine_manifest == "Woodwing Designer") selected @endif value="Woodwing Designer">Woodwing Designer</option>
                    <option @if ($device->machine_manifest == "Woodwing Sub With InCopy") selected @endif value="Woodwing Sub With InCopy">Woodwing Sub With InCopy</option>
                    <option @if ($device->machine_manifest == "Woodwing Sub With InDesign") selected @endif value="Woodwing Sub With InDesign">Woodwing Sub With InDesign</option>
                    <option @if ($device->machine_manifest == "Woodwing Retoucher") selected @endif value="Woodwing Retoucher">Woodwing Retoucher</option>
                </select>
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