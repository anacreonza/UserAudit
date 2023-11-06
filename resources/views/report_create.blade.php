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
                    <label for="system">System</label>
                </div>
                <div class="device_form_row_input">
                    <select class="form-control" name="system">
                        <option value="Manage Engine">Manage Engine</option>
                    </select>
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="endpoint">Endpoint</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="endpoint" id="endpoint" value="{{ old('endpoint')}}">
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
                    <label for="items_per_page">Items Per Page</label>
                </div>
                <div class="device_form_row_input">
                    <input class="form-control" type="text" name="items_per_page" id="items_per_page" value="{{ old('items_per_page')}}">
                </div>
            </div>
            <div>
                <button type="button submit" class="btn btn-primary">Create Report</button>
            </div>
        </form>
    </div>
</div>
@endsection