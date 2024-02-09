@extends('site')
@section('header')
    <title>Media24 - New Report</title>
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
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="container">
    <div class="viewer-title">
        <h2 class="heading">Update {{$report->report_name}}</h2>
        <div class="list-links">
            <a href="/report/delete/{{$report->id}}">Delete this report</a>
        </div>
    </div>
    <form action="/report/update/{{$report->id}}" method="post">
    @csrf
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="report_name">Report Name</label>
                </div>
                <input name="report_name" class="form-control" type="text" value="{{$report->report_name}}">
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="system">Report Type</label>
                </div>
                <div class="device_form_row_input">
                    <select class="form-control" name="report_type">
                        <option value="me_devices_by_software_id">Manage Engine - Computers by software ID</option>
                        {{-- <option value="me_devices_by_software_name">Manage Engine - Computers by software name</option> --}}
                        <option value="me_software_by_software_name" selected>Manage Engine - Software packages by name</option>
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="software_id">Software ID</label>
                </div>
                <input name="software_id" class="form-control" type="text" value="{{$report->software_id}}">
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="software_name">Software Name</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="software_name" id="software_name" value="{{$report->software_name}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="software_manufacturer">Software Manufacturer</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="software_manufacturer" id="software_manufacturer" value="{{$report->software_manufacturer}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="items_per_page">Search limit</label>
                </div>
                <input name="items_per_page" class="form-control" type="text" value="{{$report->items_per_page}}">
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Update Report</button>
            </div>
        </div>
    </form>
</div>
@endsection