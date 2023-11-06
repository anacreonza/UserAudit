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
            <div>
                <h2>Reports</h2>
            </div>
            <div class="list-links">
                <div class="list-link-item">
                    <a href="/report/create" role="button">Add a new report</a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover w-auto">
                <thead class="table-light">
                    <tr>
                        <th scope="col"><a href="/report/index/?sortby=reportname">Report Name</a></th>
                        <th scope="col"><a href="/report/index/?sortby=system">System</a></th>
                        <th scope="col"><a href="/report/index/?sortby=endpoint">Endpoint</a></th>
                        <th scope="col"><a href="/report/index/?sortby=created_at">Created At</a></th>
                        <th scope="col"><a href="/report/index/?sortby=action">Action</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                    <tr>
                        <td><a href="/report/run/{{$report->id}}" class="small">{{$report->report_name}}</a></td>
                        <td><p class="small">{{$report->system}}</p></td>
                        <td><p class="small">{{$report->endpoint}}</p></td>
                        <td><p class="small">{{$report->created_at}}</p></td>
                        <td><a href="/report/edit/{{$report->id}}" class="small">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection