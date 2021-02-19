@extends('site')
@section('header')
    <title>Create a report</title>
@endsection
@section('content')
    <h1>New Device Report</h1>
    <form action="/post_report" method="post">
        <div>
            <textarea name="report_data" id="report_data" cols="100" rows="30"></textarea>
        </div>
        <div>
            <button type="submit">Submit</button>
        </div>
    </form>
@endsection