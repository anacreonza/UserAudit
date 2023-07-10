@extends('site')
@section('header')
    <title>Lookup</title>
@endsection

@section('navbar')
    @component('navbar')
        @slot('activetab')
            Lookup
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
<div class="container-fluid">
    <div class="heading">
        <div>
            <h2>Lookup</h2>
        </div>
        @if (isset($result))
        <div class="list_links">
            <a href="/client/addclient?client={{ $item ?? '' }}" class="list_links">Add this client to managed clients</a>
        </div>
        @endif
    </div>
    <form action="/lookup/item" method="post">
        @csrf
        <div class="device_form_container">
            <div class="lookup_form_row">
                <div>
                    <select class="form-control" name="search_type">
                        <option value="user">User (LDAP)</option>
                        <option value="computer_by_user">Computer by user (Manage Engine)</option>
                        <option value="computer_by_computername">Computer by pc name (Manage Engine)</option>
                    </select>
                </div>
                <input type="text" name="item" class="form-control" value="{{$item ?? ''}}">
                <input type="submit" class="form-control btn btn-primary" value="Search">
            </div>
        </div>
    </form>
    <div class="lookup_results_table_container">
        @if (isset($result))
        <table class="lookup_results_table">
        @foreach ($result as $entry)
            <tr>
                @if (gettype($entry->value) == "NULL")
                @continue
                @endif
                <td style="border: 1px solid; width: 20%">{{$entry->name}}</td>
                <td style="border: 1px solid;">
                    @if (gettype($entry->value) == 'array')
                    @foreach($entry->value as $value)
                    @if ($entry->name == "mail")
                    <a href="mailto:{{$value}}">{{$value}}</a>
                    @continue
                    @endif
                    {{$value}}<br/>
                    @endforeach
                    @endif
                    @if (gettype($entry->value) == 'boolean')
                        @if ($entry->value == False) False @endif
                        @if ($entry->value == True) True @endif
                    @endif
                    @if (gettype($entry->value) == 'integer')
                    {{$value}}<br/>
                    @endif
                </td>
            </tr>
        @endforeach
        </table>    
        @endif
    </div>
</div>
@endsection