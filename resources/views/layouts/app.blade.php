<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Perpustakaan Tiga Serangkai')
    </title>

    {{-- =====================================================
         GLOBAL STYLE
    ====================================================== --}}

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">

    <link rel="stylesheet" href="{{ asset('css/footer.css') }}?v={{ file_exists(public_path('css/footer.css')) ? filemtime(public_path('css/footer.css')) : time() }}">

    {{-- Pastikan admin tidak mewarisi preferensi tema atau density user dari localStorage --}}
    <script>
        document.documentElement.removeAttribute('data-theme');
        document.documentElement.removeAttribute('data-density');
    </script>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/responsive-overrides.css') }}?v={{ file_exists(public_path('css/responsive-overrides.css')) ? filemtime(public_path('css/responsive-overrides.css')) : time() }}">

</head>


<body>

<div class="app">

    {{-- =====================================================
         ADMIN NAVBAR
    ====================================================== --}}

    @include('partials.navbar')


    {{-- =====================================================
         MAIN CONTENT
    ====================================================== --}}

    <main class="content">

        @yield('content')

    </main>


    @include('partials.footer', ['footerClass' => 'app-footer'])
    @include('partials.mobile-bottom-nav')

</div>


{{-- =====================================================
     GLOBAL JAVASCRIPT
====================================================== --}}

<script src="{{ asset('js/app.js') }}"></script>


{{-- =====================================================
     PAGE JAVASCRIPT
====================================================== --}}

@stack('scripts')


{{-- =====================================================
     PROTECTED PAGE
====================================================== --}}

<script>

window.addEventListener('pageshow', function (event) {

    if (event.persisted) {

        window.location.reload();

    }

});

</script>


</body>

</html>