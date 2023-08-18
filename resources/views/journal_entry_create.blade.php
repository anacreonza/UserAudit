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
<div class="container">
    <div class="viewer-title">
        <h2 class="heading">New Journal Entry</h2>
        <div class="list-links">
            @if($user_id == "new")
            <a href="/journal_entries/index/">Back to activity log</a>
            @else
            <a href="/client/view/{{$user_id}}">Back to client</a>
        </div>
        @endif
    </div>
    <div>
        <form action="/journal_entry/store/{{$user_id}}" enctype="multipart/form-data" method="post">
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
    
            <div class="custom-file">
                <label class="custom-file-label" for="attachment">Choose file</label>
                <input type="file" class="custom-file-input" name="attachment" id="attachment">
            </div>
            <br>
            <div style="padding-top: 15pt">
                <button type="button submit" class="btn btn-primary">Update Journal</button>
            </div>
            {{-- <input class="form-control" type="submit" value="Update Journal"> --}}
        </div>
        </form>

    </div>
</div>
<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
    </script>
@endsection