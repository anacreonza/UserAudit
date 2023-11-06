<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/site.css')}}">
    <script src="{{ asset('js/copyToClipboard.js')}}"></script>

    <script>
        // Refresh once an hour
        setTimeout(() => {
            document.location.reload();
        }, 3600000); 
    </script>
    @yield('header')
</head>
<body>
    <div id="app">
        <header>
        @yield('navbar')

        </header>
    @yield('content')
        <footer>
            <div class="container">
                <div class="pb-5"></div>
            </div>
        </footer>
    @yield('scripts')
    </div>
</body>
</html>