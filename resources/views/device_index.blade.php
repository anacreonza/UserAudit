@extends('site')
@section('header')
    <title>Media24 - Devices</title>
@endsection
@section('navbar')
    @component('navbar')
    @slot('activetab')
        Devices
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
        <h2 class="heading">Devices</h2>
            <div class="table_container">
                <table class="item_table">
                    <tr>
                        <th>Computer Name</th>
                        <th>Device Type</th>
                        <th>Last Activity Date</th>
                        <th>Assigned User</th>
                    </tr>
                    @foreach ($device_reports as $entry)
                    <tr>
                        <td><a href="/device/view/{{$entry->id}}">{{$entry->computername}}</a></td>
                        <td>{{$entry->devicetype}}</td>
                        <td>{{$entry->updated_at}}</td>
                        <td>{{$entry->username}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <button onclick="document.location='/device/create'">Create a new device</button>
        </div>
</div>
@endsection