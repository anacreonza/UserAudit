@extends('site')
@section('header')
    <title>Media24 - Devices</title>
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
    <div class="heading">
        <div>
            <h2>Devices <span class="badge badge-info"> {{$device_count}} </span></h2>
        </div>
        <div class="list_links">
            <a href="/device/create" role="button">Add a new device</a>
            |
            <a href="/device/export/csv">Export CSV</a>
        </div>
    </div>
    <div>
        <div class="device_list_grid_row">
            <div class="list_grid_item"><a href="/device/index/?sortby=computername">Computer Name</a></div>
            <div class="list_grid_item"><a href="/device/index/?sortby=device_model">Device Model</a></div>
            <div class="list_grid_item"><a href="/device/index/?sortby=operating_system">Operating System</a></div>
            <div class="list_grid_item"><a href="/device/index/?sortby=machine_manifest">Device Software Manifest</a></div>
            <div class="list_grid_item"><a href="/device/index/?sortby=updated_at">Last Activity Date</a></div>
            <div class="list_grid_item"><a href="/device/index/?sortby=assigned_user">Assigned User</a></div>
        </div>
        @foreach ($devices as $device)
        <div class="device_list_grid_row">
            <div class="list_grid_item"><a href="/device/view/{{$device->id}}">{{$device->computername}}</a></div>
            <div class="list_grid_item">{{$device->device_model}}</div>
            <div class="list_grid_item">{{$device->operating_system}}</div>
            <div class="list_grid_item">{{$device->machine_manifest}}</div>
            <div class="list_grid_item">{{$device->updated_at}}</div>
            <div class="list_grid_item">{{$device->name}}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection