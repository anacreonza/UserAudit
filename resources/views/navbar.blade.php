@php
$user = Auth::user();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="/">Media24&nbsp;Lifestyle&nbsp;Reporter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activetab == "JournalEntries")active @endif" href="/journal_entry/index">Activity</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activetab == "Reports")active @endif" href="/report/index/">Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activetab == "Clients")active @endif" href="/client/index/">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($activetab == "Devices")active @endif" href="/device/index/">Devices</a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link @if($activetab == "Lookup") active @endif" href="/lookup/">Lookup</a>
                </li> --}}
                <li class="nav-item navbar-item">
                    <div class="toolbar-search-input">
                        <form class="d-flex" method="POST" @if ($activetab == "JournalEntries") action="/journal_entries/search/"@endif @if ($activetab == "Reports") action="/reports/search/"@endif @if ($activetab == "Clients") action="/clients/search/"@endif @if ($activetab == "Devices") action="/devices/search/"@endif>
                            @csrf
                            
                            <input class="form-control mr-sm-2" value="{{$searchstring ?? ''}}" type="search" placeholder="@if($activetab == "Clients")Search clients @endif @if($activetab == "Devices")Search devices @endif" aria-label="Search" id="searchterm" name="searchterm" autocomplete="off" autofocus>
                            <button class="btn btn-light mini-search-button" type="submit">Search</button>
                        </form>
                    </div>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="nav-item dropdown">
                    <a class="nav-item nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{$user["name"]}}</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="/home">User Home</a></li>
                        <li><a class="dropdown-item" href="/logout">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>