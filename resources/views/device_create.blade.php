@extends('site')
@section('header')
    <title>Media24 - Create a device</title>
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
    <h2 class="heading">New Device</h2>
    <form action="/device/store" method="post">
    @csrf
        <div class="device_form_container">
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="computername">Device Name:</label>
                </div>
                <div class="device_form_row_input">
                    <input type="text" name="computername" class="form-control">
                </div>
            </div>
            <div class="device_form_row">
                <div class="device_form_row_label">
                    <label for="assigned_user_id">Assigned User:</label>
                </div>
                <select class="form-control" name="assigned_user_id">
                    <option value="0">No user allocated</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}
                        @if (isset($user_id))
                            @if ($user_id == $user->id) selected @endif
                        @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <input type="submit" value="Create Device">
        </div>
    </form>
</div>
@endsection