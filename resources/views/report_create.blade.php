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
        <h2 class="heading">New Report</h2>
    </div>
    <div class="device_form_container">
        <form action="/report/store" method="post">
            @csrf
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="ad_user">Report Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="report_name" value="{{ old('report_name') }}">
                </div>
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
                    <label for="software_name">Software Name</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="software_name" id="software_name" value="{{ old('software_name')}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="software_id">Software ID</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="software_id" id="software_id" value="{{ old('software_id')}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="software_manufacturer">Software Manufacturer</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="software_manufacturer" id="software_manufacturer" value="{{ old('software_manufacturer')}}">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="items_per_page">Items Per Page</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="items_per_page" id="items_per_page" value="{{ old('items_per_page')}}" default="50">
                </div>
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Create Report</button>
            </div>
        </form>
    </div>
</div>
@endsection