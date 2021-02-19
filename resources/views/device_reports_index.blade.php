@extends('site')
@section('header')
    <title>Devices</title>
    <style>
        .device_table {
            max-width: 800pt;
            border-style: solid;
            border-width: 1pt;
            border-color: lightgrey;
            border-radius: 5pt;
        }
        .device_grid_header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            font-weight: 700;
            padding: 6pt;
            border-bottom: 1pt solid lightgrey;
            background-color: #f2f2f2;
            font-size: 11pt;
        }
        .device_entry {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            padding: 6pt;
            font-size: 11pt;
            border-bottom: 1pt solid lightgrey;
        }
        .device_entry:hover{
            background-color: #eeeeff;
            cursor: pointer;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
@endsection
@section('content')
    <h1>Registered Devices</h1>
    <div>
        <div class="device_table">
            <div class="device_grid_header">
                <div>Computer Name</div>
                <div>User Name</div>
                <div>Report Date</div>
            </div>
            @foreach ($device_reports as $entry)
                <a href="/show_device_report/{{$entry->id}}">
                    <div class="device_entry">
                        <div>{{$entry->computername}}</div>
                        <div>{{$entry->username}}</div>
                    </div>
                </a>
            @endforeach
        </div>
        <a href="/create_device_report">Create a report manually</a>
    </div>
@endsection