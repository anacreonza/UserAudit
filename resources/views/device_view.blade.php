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
    </div>
    <div class="item-view-right">
        <h2>{{$device->computername}}</h2>
        <p>Serial Number: {{$device->serial_no}}</p>
        <p>Assigned To: <a href="/client/view/{{$client->id}}">{{$client->name}}</a></p>
        @if (str_contains($device->operating_system, "Mac OS") == True )
        <p>Device Model: <a href="https://www.everymac.com/ultimate-mac-lookup/?search_keywords={{$device->device_model}}">{{$device->device_model}}</a></p>
        @else
        <p>Device Model: {{$device->device_model}}</p>
        <p>Operating System: {{$device->operating_system}}</p>
        <p>Device Software Manifest: {{$device->machine_manifest}}</p>
        @endif
        @if (str_contains($device->operating_system, "Mac OS") == True )
        <p><a href="{{env('MR_URL')}}/index.php?/clients/detail/{{$device->serial_no}}#tab_summary" target=”_blank”>View Mac in Munkireport</a></p>
        @endif
        <p><a href="{{env('ME_URL')}}/webclient#/uems/inventory/computers" target=”_blank”>Open Manage Engine</a></p>
        <p><a href="/retrieve/mr/{{$device->serial_no}}" target=”_blank”>Retrieve Mac details from Munkireport</a></p>
    </div>
</div>
@endsection