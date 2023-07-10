<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link active" href="/">Media24 Lifestyle Reporter</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "JournalEntries") active @endif" href="/journal_entry/index">Activity</a>
      </li>
      {{-- <li class="nav-item">
        <a class="nav-link @if($activetab == "Reports") active @endif" href="/report/index/">Reports</a>
      </li> --}}
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Clients") active @endif" href="/client/index/">Clients</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Devices") active @endif" href="/device/index/">Devices</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Lookup") active @endif" href="/lookup/">Lookup</a>
      </li>
    </ul>
    <div class="toolbar-searchbox">
      <form class="form-inline my-2 my-lg-0"
        @if ($activetab == "Lookup") style="Display: None" @endif
        @if ($activetab == "JournalEntries") action="/journal_entries/search/" @endif
        @if ($activetab == "Reports") action="/reports/search/" @endif
        @if ($activetab == "Clients") action="/clients/search/" @endif
        @if ($activetab == "Devices") action="/devices/search/" @endif
        method="POST">
        @csrf
        <input class="form-control mr-sm-2" value="{{$searchstring ?? ''}}" type="search" placeholder="@if($activetab == "Clients")Search clients @endif @if($activetab == "Devices")Search devices @endif"
        aria-label="Search" id="searchterm" name="searchterm" autocomplete="off" autofocus>
        <button class="btn btn btn-light my-2 my-sm-0" type="submit">Search</button>
      </form>
    </div>
    <div>
      <li class="nav-item dropdown">
        @php
        $user = Auth::user();
        @endphp
        <a class="nav-link dropdown-toggle navbar-text" href="#" id="navbardrop" data-toggle="dropdown">{{$user["name"]}}</a>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="/home">User Home</a>
          <a class="dropdown-item" href="/logout">Log Out</a>
        </div>
      </li>
    </div>
  </div>
</nav>