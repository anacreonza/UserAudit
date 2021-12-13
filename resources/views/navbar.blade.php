<nav class="navbar navbar-expand-sm bg-dark navbar-dark">

    <!-- Links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link active" href="/">Media24 Lifestyle Reporter</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "JournalEntries") active @endif" href="/journalentry/index/">Activity</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Reports") active @endif" href="/report/index/">Reports</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Users") active @endif" href="/user/index/">Users</a>
      </li>
      <li class="nav-item">
        <a class="nav-link @if($activetab == "Devices") active @endif" href="/device/index/">Devices</a>
      </li>
    </ul>
  </nav>