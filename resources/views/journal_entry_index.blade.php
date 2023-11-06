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
    <div class="container">
        <div class="viewer-title">
            <div><h2>Activity</h2></div>
            <div class="list-links">
                <a href="/journal_entry/create/new" role="button">Add a new journal entry</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <th scope="col">Date</th>
                    <th scope="col">User</th>
                    <th scope="col">Admin Name</th>
                    <th scope="col">Activity</th>
                </thead>
                <tbody>
                    @foreach ($journal_entries as $entry)
                    <tr>
                        <td class="journal_list_grid_item"><p class="small">{{$entry->updated_at}}</p></td>
                        <td class="journal_list_grid_item"><a href="/client/view/{{$entry->ad_user}}" class="small">{{$entry->name}}</a></td>
                        <td class="journal_list_grid_item"><p class="small">{{$entry->adminName}}</p></td>
                        <td class="journal_list_grid_item"><p class="small">{{$entry->journal_entry}}</p></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">   
            {{$journal_entries->links()}}
        </div>
    </div>
@endsection