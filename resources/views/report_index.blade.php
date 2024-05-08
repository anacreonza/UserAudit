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
                <h2>Reports <span class="badge bg-info"> {{$reports->total()}} </span></h2>
            </div>
            <div class="list-links">
                <div class="list-link-item">
                    <a href="/report/create" role="button">Add a new report</a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col"><a href="/report/index/?reports_sortby=report_name">Report Name</a></th>
                        {{-- <th scope="col"><a href="/report/index/?reports_sortby=report_type">Report Type</a></th> --}}
                        <th scope="col"><a href="/report/index/?reports_sortby=software_name">Software Name</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=software_name">Software Manufacturer</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=created_at">Created At</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=modified_at">Modified At</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=installs">Installations</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=count">Versions</a></th>
                        <th scope="col"><a href="/report/index/?reports_sortby=action">Action</a></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $report)
                    <tr>
                        <td><a href="/report/run/{{$report->id}}" class="small">{{$report->report_name}}</a></td>
                        {{-- <td><p class="small">{{$report->report_type}}</p></td> --}}
                        <td><p class="small">{{$report->software_name}}</p></td>
                        <td><p class="small">{{$report->software_manufacturer}}</p></td>
                        <td><p class="small">{{$report->created_at}}</p></td>
                        <td><p class="small">{{$report->updated_at}}</p></td>
                        <td><p class="small">{{$report->installs}}</p></td>
                        <td><p class="small">{{$report->count}}</p></td>
                        <td><a href="/report/edit/{{$report->id}}" class="small">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{$reports->links()}}
        </div>
    </div>
@endsection