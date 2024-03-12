@extends('site')
@section('header')
    <title>Media24 - Reports</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Reports
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
        @if ($result->report->report_type == "me_software_by_software_name")
        <div class="viewer-title">
            <div>
                <h3>{{$result->report->report_name}} <span class="badge bg-info"> {{$result->installs_total}}</span></h3>
                <h4>Versions: <span class="badge bg-info">{{$result->total}}</span></h4>
            </div>
            <div class="list-links">
                <p>Query Items Limit: {{$result->limit}} | Page Number: {{$result->page}}</p>
            </div>
        </div>
        <table class="table table-responsive">
            <thead class="table-light">
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=manufacturer_name">Software Manufacturer</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=software_name">Software Package</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=software_version">Software Version</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=managed_sw_id">Managed Software ID</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=human_readable_detected_time">Last Detected</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=network_installations">Installations</a></th>
            </thead>
            <tbody>
                @foreach ($result->filtered_software as $package)
                <tr>
                    <td><p class="small">{{$package->manufacturer_name}}</p></td>
                    <td><p class="small">{{$package->software_name}}</a></td>
                    <td><p class="small">{{$package->software_version}}</p></td>
                    <td><a href="/report/find_computers_by_software_id?software_id={{$package->managed_sw_id}}&software_name={{$package->software_name}}&software_version={{$package->software_version}}" class="small">{{$package->managed_sw_id}}</a></td>
                    <td><p class="small">{{$package->human_readable_detected_time}}</p></td>
                    <td><p class="small">{{$package->network_installations}}</p></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="viewer-title">
            <h3>{{$result->report->report_name}} <span class="badge bg-info">{{$result->total}} Devices</span></h3>
            <div class="list-links">
                <p>Query Items Limit: {{$result->report->limit}} | Page Number: {{$result->report->page}}</p>
            </div>
        </div>
        <table class="table table-responsive">
            <thead class="table-light">
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=resource_name">Resource Name</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=model">Model</a></th>
                <th scope="col">Logged On User</th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=ip_address">IP Address</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=software_name">Software Name</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=software_version">Software Version</a></th>
                <th scope="col"><a href="/report/run/{{$result->report->id}}?sortby=last_successful_scan">Last Scan Date</a></th>
            </thead>
            <tbody>
                @foreach ($result->computers as $computer)
                <tr>    
                    <td><a href="/device/view/{{$computer->resource_name}}" class="small">{{$computer->resource_name}}</a></td>
                    <td><p class="small">{{$computer->model}}</p></td>
                    <td><p class="small"><a href="/client/view/{{$computer->agent_logged_on_users}}">{{$computer->agent_logged_on_users}}</a></p></td>
                    <td><p class="small">{{$computer->ip_address}}</p></td>
                    <td><p class="small">{{$computer->software_name}}</p></td>
                    <td><p class="small">{{$computer->software_version}}</p></td>
                    <td><p class="small">{{$computer->relative_last_scan_date}}</p></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
@endsection