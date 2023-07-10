@extends('site')
@section('header')
    <title>Report Viewer</title>
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
<div class="item-container">
    <div class="item-view-left">
        <p><a href="/device/delete/{{$device->id}}">Delete device</a></p>
        <p><a href="/device/edit/{{$device->id}}">Edit device</a></p>
        @php
        if (str_contains($device->operating_system, "mac")){
            $device_is_mac = True;
        } else {
            $device_is_mac = False;
        }
        @endphp
        @if ($device_is_mac)
        <p><a href="{{env('MR_URL')}}/index.php?/clients/detail/{{$device->serial_no}}#tab_summary" target=”_blank”>View Mac in Munkireport</a></p>
        @endif
    </div>
    <div class="item-view-right">
        <h2>{{$device->computername}}</h2>
        <p>Assigned To: <a href="/client/view/{{$client->id}}">{{$client->name}}</a></p>
        <p>Device Type: {{$device->device_type}}</p>
        @if (isset($device->device_manufacturer))
        <p>Device Manufacturer: {{$device->device_manufacturer}}</p>
        @endif
        @if (str_contains($device->operating_system, "mac") == True )
        <p>Device Model: <a href="https://www.everymac.com/ultimate-mac-lookup/?search_keywords={{$device->device_model}}">{{$device->device_model}}</a></p>
        @else
        <p>Device Model: {{$device->device_model}}</p>
        @endif
        <p>Serial Number: {{$device->serial_no}}</p>
        <p>Operating System: {{$device->operating_system}}</p>
        @if (isset($device->os_version))
        <p>Operating System Version: {{$device->os_version}}</p>
        @endif
        @if (isset($device->ram))
        <p>Installed Memory: {{$device->ram}} MB</p>
        @endif
        @if (isset($device->disk_total_size))
        <p>Disk Total Size: {{$device->disk_total_size}} GB</p>
        @endif
        @if (isset($device->disk_percent_free))
        <p>Disk Percent Free: {{$device->disk_percent_free}}</p>
        @endif
        <p>Device Software Manifest: {{$device->machine_manifest}}</p>
    </div>
</div>
@endsection