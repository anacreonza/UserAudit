@extends('site')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('You are logged in!') }}
                    <ul>
                        <li><a href="/client/index/">Client Index</a></li>
                        <li><a href="/phpinfo.php">PHP Info</a></li>
                        <li><a href="/logout">Log Out</a></li>
                    </ul>
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
