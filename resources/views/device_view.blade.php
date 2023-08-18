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
<div class="container">
    <div class="viewer-title">
        <div>
            <h1>{{strtoupper($device->computername)}}</h1>
        </div>
        <div class="list-links">
            <a href="/device/delete/{{$device->id}}" onclick="return confirm('Are you sure you wish to delete this device?')">Delete device</a> |
            <a href="/device/edit/{{$device->id}}">Edit device</a>
            @php
            if (str_contains($device->operating_system, "mac")){
                $device_is_mac = True;
            } else {
                $device_is_mac = False;
            }
            @endphp
            @if ($device_is_mac)
            | <a href="{{env('MR_URL')}}/index.php?/clients/detail/{{$device->serial_no}}#tab_summary" target=”_blank”>View Mac in Munkireport</a>
            @endif
        </div>        
    </div>
    <div>
        <h2>Device Details</h2>
        <hr>
        <div class="details-container">
            <div>
                <b><p>Current User:</b> <a href="/client/view/{{$client->ad_user}}">{{$client->name}}</a></p>
            </div>
            <div>
                <b><p>Device Type:</b> {{$device->device_type}}</p>
            </div>
            @if (isset($device->device_manufacturer))
            <div><p><b>Device Manufacturer:</b> {{$device->device_manufacturer}}</p></div>
            @endif
            @if (str_contains($device->operating_system, "mac") == True )
            <div><p><b>Device Model:</b> <a href="https://www.everymac.com/ultimate-mac-lookup/?search_keywords={{$device->device_model}}">{{$device->device_model}}</a></p></div>
            @else
            <div><p><b>Device Model:</b> {{$device->device_model}}</p></div>
            @endif
            <div><p><b>Serial Number:</b> {{$device->serial_no}}</p></div>
            <div><p><b>OS:</b> {{$device->operating_system}}</p></div>
            @if (isset($device->os_version))
            <div><p><b>Operating System Version:</b> {{$device->os_version}}</p></div>
            @endif
            @if (isset($device->ram))
            <div><p><b>Installed Memory:</b> {{$device->ram}} MB</p></div>
            @endif
            @if (isset($device->disk_total_size))
            <div><p><b>Disk Total Size:</b> {{$device->disk_total_size}} GB</p></div>
            @endif
            @if (isset($device->disk_percent_free))
            <div><p><b>Disk Percent Free:</b> {{$device->disk_percent_free}}%</p></div>
            @endif
            @if (isset($device->machine_manifest))
            <div><p><b>Device Software Manifest:</b> {{$device->machine_manifest}}</p></div>
            @endif
        </div>
        <br>
        <div class="software_list">
            <h4>Installed Software</h4>
            <hr>
            <table class="software_table">
                <colgroup>
                    <col span="1" style="width: 30%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 10%;">
                    <col span="1" style="width: 15%;">
                </colgroup>
                <tr>
                    <th>Software Name</th>
                    <th>Version</th>
                    <th>Installed Date</th>
                    <th>Architecture</th>
                    <th>Manufacturer</th>
                </tr>
                @foreach ($software as $package)
                    <tr>
                        <td>{{($package->software_name)}}</td>
                        <td>{{($package->software_version)}}</td>
                        <td>{{($package->installed_date)}}</td>
                        <td>{{($package->architecture)}}</td>
                        <td>{{($package->manufacturer_name)}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection