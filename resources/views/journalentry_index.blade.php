@extends('site')
@section('header')
    <title>Media24 - Journals</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        JournalEntries
    @endslot
    @endcomponent
@endsection
@section('content')
<div class="container-fluid">
    <h2 class="heading">Activity</h2>
</div>
@endsection