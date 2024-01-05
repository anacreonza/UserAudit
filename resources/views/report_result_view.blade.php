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
        <div class="viewer-title">
            <h3>{{$report->report_name}} <span class="badge bg-info">{{$response->total}} Devices</h3>
            <div class="list-links">
                <p>Query Items Limit: {{$response->limit}} | Page Number: {{$response->page}}</p>
            </div>
        </div>
        <table class="table table-responsive">
            <thead class="table-light">
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=resource_name">Resource Name</a></th>
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=model">Model</a></th>
                <th scope="col">Logged On User</th>
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=ip_address">IP Address</a></th>
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=software_name">Software Name</a></th>
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=software_version">Software Version</a></th>
                <th scope="col"><a href="/report/run/{{$report->id}}?sortby=last_successful_scan">Last Scan Date</a></th>
            </thead>
            <tbody>
                @foreach ($response->computers as $computer)
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
    </div>
@endsection