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
<div class="container">
    <div class="viewer-title">
        <h2 class="heading">Update {{$device->computername}}</h2>
        <div class="list-links">
            <a href="/device/delete/{{$device->id}}">Delete this device</a>
            <a href="/device/view/{{$device->id}}">View device</a>
            <a href="/device/get_me_details/{{$device->id}}">Get machine specs</a>
        </div>
    </div>
    <div>
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