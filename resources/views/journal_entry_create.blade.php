@extends('site')
@section('header')
    <title>Media24 - Edit Journal</title>
@endsection

@section('navbar')
    @component('navbar')
        @slot('activetab')
            Users
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
<div class="item-container">
    @if($user_id == "new")
    <div class="item-view-left">
        <a href="/journal_entries/index/">Back to activity log</a>
    </div>
    @else
    <div class="item-view-left">
        <a href="/client/view/{{$user_id}}">Back to client</a>
    </div>
    @endif
    <div class="item-view-right">
        <h2 class="heading">New Journal Entry</h2>
        <form action="/journal_entry/store/{{$user_id}}" method="post">
        @csrf
            @if($user_id == "new")
                <label for="journal_entry">Client Name:</label>
                <select class="form-control" name="id">
                    @foreach ($clients as $client)
                        <option value="{{$client->id}}">{{$client->name}}</option>
                    @endforeach
                </select>
            @endif
            <label for="journal_entry">Entry details:</label>
            <textarea class="form-control" name="journal_entry" rows="5" value=""></textarea><br>
            <button type="button submit" class="btn btn-primary">Update Journal</button>
            {{-- <input class="form-control" type="submit" value="Update Journal"> --}}
        </div>
        </form>
    </div>
</div>
@endsection