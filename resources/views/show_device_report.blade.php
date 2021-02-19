@extends('site')
@section('header')
    <title>Report Viewer</title>
@endsection
@section('content')
    <div class="content">
        <h1>Reports</h1>
        <ul>
            <li>Username</li>
            <li>ComputerName</li>
            <li>Woodwing Settings</li>
                <ul>
                    <li>Package Version</li>
                    <li>Server Entries</li>
                    <li>Application Mappings</li>
                </ul>
            <li>Woodwing Software</li>
                <ul>
                    <li>Application</li>
                    <li>Version</li>
                </ul>
            <li>Adobe Software</li>
                <ul>
                    <li>Application</li>
                    <li>Version</li>
                </ul>
            <li>Drive Mappings</li>
                <ul>
                    <li>Local Path</li>
                    <li>Remote Path</li>
                </ul>
            <li>Desktop Shortcuts</li>
                <ul>
                    <li>Shortcut Name</li>
                </ul>
        </ul>
    </div>
@endsection