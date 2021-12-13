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
<div class="container-fluid">
    <h2 class="heading">New Device</h2>
    <form action="/device/store" method="get">
        <div>
            <div class="device_form_container">
                <div class="device_form_row">
                    <div class="device_form_row_label">
                        <label for="username">Device Name:</label>
                    </div>
                    <div class="device_form_row_input">
                        <input type="text" name="devicename">
                    </div>
                </div>
                <div class="device_form_row">
                    <div class="device_form_row_label">
                        <label for="username">Assigned User:</label>
                    </div>
                    <div class="device_form_row_input">
                        <input type="text" name="username" style="width: 80%">
                    </div>
                </div>
                <div class="device_form_row">
                    <div class="device_form_row_label">
                        <label for="username">Device Type:</label>
                    </div>
                    <div class="device_form_row_input">
                        <select name="" id="">
                            <option value="Mac">Mac</option>
                            <option value="WindowsPC">Windows PC</option>
                            <option value="MobileDevice">Mobile Device</option>
                        </select>
                    </div>
                </div>
            </div>
            <div>
                <input type="submit" value="Create Device">
            </div>
        </form>
    </div>
</div>
@endsection