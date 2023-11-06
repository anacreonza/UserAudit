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
            <h1>{{strtoupper($device_details->scancomputer->resource_name)}}</h1>
        </div>
        <div class="list-links">
            <div>
                @if(isset($device))
                <a href="/device/delete/{{$device->id}}" onclick="return confirm('Are you sure you wish to delete this device?')">Delete device</a> |
                <a href="/device/edit/{{$device->id}}">Edit device</a>
                @endif
                @php
                if (str_contains($device_details->computer_os_summary->os_name, "mac")){
                    $device_is_mac = True;
                } else {
                    $device_is_mac = False;
                }
                @endphp
                @if ($device_is_mac)
                | <a href="{{env('MR_URL')}}/index.php?/clients/detail/{{$device_details->computer_hardware_summary->serial_number}}#tab_summary" target=”_blank”>View Mac in Munkireport</a>
                @endif
            </div>
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
                <b><p>Device Type:</b> {{$device_details->computer_hardware_summary->device_type}}</p>
            </div>
            @if (isset($device_details->computer_hardware_summary->device_manufacturer))
            <div><p><b>Device Manufacturer:</b> {{$device_details->computer_hardware_summary->device_manufacturer}}</p></div>
            @endif
            @if ($device_is_mac == True )
            <div><p><b>Device Model:</b> <a href="https://www.everymac.com/ultimate-mac-lookup/?search_keywords={{$device_details->computer_hardware_summary->device_model}}">{{$device_details->computer_hardware_summary->device_model}}</a></p></div>
            @else
            <div><p><b>Device Model:</b> {{$device_details->computer_hardware_summary->device_model}}</p></div>
            @endif
            <div><p><b>Serial Number:</b> {{$device_details->computer_hardware_summary->serial_number}}</p></div>
            <div><p><b>OS:</b> {{$device_details->computer_os_summary->os_name}}</p></div>
            @if (isset($device_details->computer_os_summary->os_name))
            <div><p><b>Operating System Version:</b> {{$device_details->computer_os_summary->os_version}}</p></div>
            @endif
            @if (isset($device_details->computer_hardware_summary->memory))
            <div><p><b>Installed Memory:</b> {{$device_details->computer_hardware_summary->memory}} MB</p></div>
            @endif
            @if (isset($device_details->computer_disk_summary->total_size))
            <div><p><b>Disk Total Size:</b> {{$device_details->computer_disk_summary->total_size}} GB</p></div>
            @endif
            @if (isset($device_details->computer_disk_summary->percent_free))
            <div><p><b>Disk Percent Free:</b> {{$device_details->computer_disk_summary->percent_free}}%</p></div>
            @endif
            @if (isset($device->machine_manifest))
            <div><p><b>Device Software Manifest:</b> {{$device->machine_manifest}}</p></div>
            @endif
            <div><p><b>Manage Engine Resource ID:</b> {{$device_details->scancomputer->resource_id}}</p></div>
            <div><p><b>Last Successful Scan:</b> {{$device_details->last_scan_ago}}</p></div>
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
                    <col span="1" style="width: 8%;">
                    <col span="1" style="width: 12%;">
                    <col span="1" style="width: 5%;">
                </colgroup>
                <tr>
                    <th>Software Name</th>
                    <th>Version</th>
                    <th>Installed Date</th>
                    <th>Architecture</th>
                    <th>Manufacturer</th>
                    <th>Software ID</th>
                </tr>
                @foreach ($device_details->software as $package)
                    <tr>
                        <td>{{($package->software_name)}}</td>
                        <td>{{($package->software_version)}}</td>
                        <td>{{($package->installed_date)}}</td>
                        <td>{{($package->architecture)}}</td>
                        <td>{{($package->manufacturer_name)}}</td>
                        <td>{{($package->software_id)}}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
@section('footer')
@endsection