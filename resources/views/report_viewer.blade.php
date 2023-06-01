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
            {!!$report->html_report!!}
        </div>
    </div>
    @endsection