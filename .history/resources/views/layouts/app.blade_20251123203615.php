<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @isset($title)
            {{ $title !== '' ? $title . ' | Multipurpose Bootstrap Dashboard Template' : 'CORK Admin | Multipurpose Bootstrap Dashboard Template' }}
        @endisset
    </title>

    <!-- Favicon -->
    <link rel="icon" href="{{ Vite::asset('resources/images/favicon.ico') }}" type="image/x-icon"/>

    <!-- Global Mandatory Styles -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/src/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/src/waves/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/src/highlight/styles/monokai-sublime.css') }}">

    <!-- Theme SCSS -->
    @vite([
        'resources/scss/layouts/modern-dark-menu/light/loader.scss',
        'resources/scss/layouts/modern-dark-menu/dark/loader.scss',
        'resources/scss/light/assets/main.scss',
        'resources/scss/dark/assets/main.scss',
        'resources/scss/light/plugins/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/scss/dark/plugins/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/scss/layouts/modern-dark-menu/light/structure.scss',
        'resources/scss/layouts/modern-dark-menu/dark/structure.scss'
    ])

    <style>
        body:not(.dark) .logo-light { display: block; }
        body:not(.dark) .logo-dark { display: none; }
        body.dark .logo-light { display: none; }
        body.dark .logo-dark { display: block; }
    </style>
</head>
<body class="{{ Request::routeIs('collapsed') ? 'alt-menu' : '' }}">

    <!-- LOADER -->
    <div id="load_screen">
        <div class="loader">
            <div class="loader-content">
                <div class="spinner-grow align-self-center"></div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!-- SIDEBAR -->
        @include('layouts.sidebar')

        <!-- NAVBAR -->
        @include('layouts.navbar')

        <!-- CONTENT AREA -->
        <div id="content" class="main-content">
            @yield('content')
        </div>

        <!-- FOOTER -->
        @include('layouts.footer')
    </div>

    <!-- GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('plugins/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('plugins/src/mousetrap/mousetrap.min.js') }}"></script>
    <script src="{{ asset('plugins/src/waves/waves.min.js') }}"></script>
    <script src="{{ asset('plugins/src/highlight/highlight.pack.js') }}"></script>

    <!-- VITE APP JS -->
    @vite(['resources/layouts/modern-dark-menu/app.js'])

</body>
</html>
