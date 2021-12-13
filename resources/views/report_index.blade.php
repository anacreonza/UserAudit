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
        <h2 class="heading">Reports</h2>
        <div class="table_container">
            <table class="item_table">
                <tr>
                    <th></th>
                    <th>Last Activity Date</th>
                    <th>Machine Name</th>
                    <th>User Name</th>
                </tr>
                @foreach ($reports as $report)
                <tr>
                    <td><a href="/report/view/{{$report->id}}">View</a></td>
                    <td>{{$report->updated_at}}</td>
                    <td>{{$report->computer_name}}</td>
                    <td>{{$report->user_name}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection