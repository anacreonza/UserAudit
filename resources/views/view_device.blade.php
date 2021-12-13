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
    <div class="item-container">
        <div class="item-view-left">
            <a href="/device/delete/{{$device->id}}">Delete device</a>
        </div>
        <div class="item-view-right">
            <h2>{{$device->computername}}</h2>
            <p>Assigned User: {{$device->username}}</p>
            <p>Device Type: {{$device->type}}</p>
        </div>
</div>
@endsection