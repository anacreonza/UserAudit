@extends('site')
@section('header')
    <title>Media24 - Journal</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        JournalEntries
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
            <div><h2>Activity</h2></div>
            <div class="list_links">
                <a href="/journal_entry/create/new" role="button">Add a new journal entry</a>
            </div>
        </div>
        <div>
            <div class="journal_list_grid_row">
                <div class="journal_list_grid_item">Date</div>
                <div class="journal_list_grid_item">User</div>
                <div class="journal_list_grid_item">Admin Name</div>
                <div class="journal_list_grid_item">Activity</div>
            </div>
            @foreach ($journal_entries as $entry)
            <div class="journal_list_grid_row">
                <div class="journal_list_grid_item">{{$entry->updated_at}}</div>
                <div class="journal_list_grid_item"><a href="/client/view/{{$entry->user_id}}">{{$entry->name}}</a></div>
                <div class="journal_list_grid_item">{{$entry->adminName}}</div>
                <div class="journal_list_grid_item">{{$entry->journal_entry}}</div>
            </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection