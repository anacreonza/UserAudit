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
    <div class="container-fluid">
        <div class="heading">
            <div>
                <h2>Reports</h2>
            </div>
        </div>
        <div>
            <div class="report_list_grid_row">
                <div class="report_list_grid_item">Activity Date</div>
                <div class="report_list_grid_item">Machine Name</div>
                <div class="report_list_grid_item">User Name</div>
                <div class="report_list_grid_item"></div>
            </div>
            @foreach ($reports as $report)
            <div class="report_list_grid_row">
                <div class="list_grid_item">{{$report->updated_at}}</div>
                <div class="list_grid_item">{{$report->computer_name}}</div>
                <div class="list_grid_item">{{$report->user_name}}</div>
                <div class="list_grid_item"><a href="/report/view/{{$report->id}}">View</a></div>
            </div>
            @endforeach
        </div>
    </div>
@endsection