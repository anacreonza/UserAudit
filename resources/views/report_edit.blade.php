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
                    <label for="assigned_user_id">Report Name</label>
                </div>
                <input name="report_name" class="form-control" type="text" value="{{$report->report_name}}">
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Endpoint</label>
                </div>
                <input name="endpoint" class="form-control" type="text" value="{{$report->endpoint}}">
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Software ID</label>
                </div>
                <input name="software_id" class="form-control" type="text" value="{{$report->software_id}}">
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Items Per Page</label>
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