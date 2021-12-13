@extends('site')
@section('header')
    <title>Report Viewer</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Reports
    @endslot
    @endcomponent
@endsection
@section('content')
    <div class="item-container">
        <div class="item-view-left">
            <a href="/report/delete/{{$report->id}}">Delete this report</a>
        </div>
        <div class="item-view-right">
            <h1>Report data</h1>
            <pre>{{var_dump(json_decode($report->report_data, JSON_PRETTY_PRINT))}}</pre>
        </div>
    </div>
    @endsection