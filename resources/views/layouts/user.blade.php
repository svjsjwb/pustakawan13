<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Pustakawan')
    </title>


    {{-- GLOBAL USER STYLE --}}

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">

    <link rel="stylesheet" href="{{ asset('css/user-navbar.css') }}">


    {{-- PAGE STYLE --}}

    @stack('styles')

</head>


<body>

    <div class="app">

        {{-- USER NAVBAR --}}

        @include('partials.user-navbar')


        {{-- CONTENT --}}

        <main class="content">

            @yield('content')

        </main>


        {{-- FOOTER --}}

        @include('partials.footer')

    </div>


    {{-- =====================================================
         GLOBAL JS
    ====================================================== --}}

    <script src="{{ asset('js/app.js') }}"></script>


    {{-- =====================================================
         USER NAVBAR JS
    ====================================================== --}}

    <script src="{{ asset('js/user-navbar.js') }}"></script>


    {{-- =====================================================
         PAGE JS
    ====================================================== --}}

    @stack('scripts')


    {{-- =====================================================
         PROTECTED USER PAGE
    ====================================================== --}}

    <script>

        window.addEventListener(
            'pageshow',
            function (event) {

                if (event.persisted) {

                    window.location.reload();

                }

            }
        );

    </script>

</body>

</html>