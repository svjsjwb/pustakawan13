<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Perpustakaan Tiga Serangkai')
    </title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    @stack('styles')
    @stack('scripts')

</head>

<body>

    <div class="app">

        @include('partials.navbar')

        <main class="content">

            @yield('content')

        </main>

        @include('partials.footer')

    </div>

    <script src="{{ asset('js/app.js') }}"></script>

</body>

</html>