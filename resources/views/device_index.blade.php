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
<div class="container">
    <div class="viewer-title">
        <div>
            <h2>Devices <span class="badge bg-info"> {{$device_count}} </span></h2>
        </div>
        <div class="list-links">
            {{-- <div class="list-link-item">
                <form action="">
                    <label for="pagination" class="form-label">Items per page</label>
                    <div>
                        <select name="pagination" id="pagination" class="form-select pagi-selector">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                </form>
            </div> --}}
            <div class="list-link-item">
                <a href="/device/create" role="button">Add a new device</a> |
                <a href="/device/export/csv">Export CSV</a>
            </div>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover w-auto">
            <thead class="table-light">
                <tr>
                    <th scope="col"><a href="/device/index/?sortby=computername">Computer Name</a></th>
                    <th scope="col"><a href="/device/index/?sortby=device_model">Device Model</a></th>
                    <th scope="col"><a href="/device/index/?sortby=operating_system">Operating System</a></th>
                    <th scope="col"><a href="/device/index/?sortby=machine_manifest">Device Software Manifest</a></th>
                    <th scope="col"><a href="/device/index/?sortby=updated_at">Last Activity Date</a></th>
                    <th scope="col"><a href="/device/index/?sortby=assigned_user">Assigned User</a></th>
                    <th scope="col"><a href="/device/index/?sortby=me_res_id">In Manage Engine?</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr>
                    <td><a href="/device/view/{{$device->computername}}" class="small">{{$device->computername}}</a></td>
                    <td><p class="text-muted small">{{$device->device_model}}</p></td>
                    <td><p class="text-muted small">{{$device->operating_system}}</p></td>
                    <td><p class="text-muted small">{{$device->machine_manifest}}</p></td>
                    <td><p class="text-muted small text-nowrap">{{$device->updated_at}}</p></td>
                    <td><p class="text-muted small"><a href="/client/view/{{$device->ad_user}}">{{$device->name}}</a></td>
                    @if ($device->me_res_id)
                    <td><p class="small green">Yes</p></td>
                    @else
                    <td><p class="small">No</p></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ( ! is_array($devices))
    <div class="pagination">
        {{$devices->links()}}
    </div>
    @endif
</div>
@endsection