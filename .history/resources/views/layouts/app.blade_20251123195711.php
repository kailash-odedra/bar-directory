<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>
        @isset($title)
            @if ($title !== '')
                {{ $title }} | Multipurpose Bootstrap Dashboard Template
            @else
                CORK Admin | Multipurpose Bootstrap Dashboard Template
            @endif
        @endisset
    </title>
    <link rel="icon" type="image/x-icon" href="{{ Vite::asset('resources/images/favicon.ico') }}"/>

    <!-- Loader SCSS -->
    @vite(['resources/scss/layouts/modern-dark-menu/light/loader.scss'])
    @vite(['resources/scss/layouts/modern-dark-menu/dark/loader.scss'])
    @vite(['resources/layouts/modern-dark-menu/loader.js'])

    <!-- GLOBAL STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/src/bootstrap/css/bootstrap.min.css') }}">
    @vite(['resources/scss/light/assets/main.scss'])
    @vite(['resources/scss/dark/assets/main.scss'])
    @vite(['resources/scss/light/plugins/perfect-scrollbar/perfect-scrollbar.scss'])
    @vite(['resources/scss/dark/plugins/perfect-scrollbar/perfect-scrollbar.scss'])
    <link rel="stylesheet" href="{{ asset('plugins/src/waves/waves.min.css') }}">
    @vite(['resources/scss/layouts/modern-dark-menu/light/structure.scss'])
    @vite(['resources/scss/layouts/modern-dark-menu/dark/structure.scss'])
    <link rel="stylesheet" href="{{ asset('plugins/src/highlight/styles/monokai-sublime.css') }}">

    <style>
        body:not(.dark) .logo-light { display: block; }
        body:not(.dark) .logo-dark { display: none; }
        body.dark .logo-light { display: none; }
        body.dark .logo-dark { display: block; }
    </style>

    <!-- PAGE LEVEL STYLES -->
    @yield('styles')
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

    <!-- NAVBAR -->
    @includeUnless(Request::routeIs('blank'), 'layouts.navbar')

    <!-- MAIN CONTAINER -->
    <div class="main-container" id="container">

        @includeUnless(Request::routeIs('blank'), 'layouts.sidebar')

        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                <div class="middle-content p-0">
                    @yield('content')
                </div>
            </div>
        </div>

        @includeUnless(Request::routeIs('blank'), 'layouts.footer')
    </div>

    <!-- GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('plugins/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('plugins/src/mousetrap/mousetrap.min.js') }}"></script>
    <script src="{{ asset('plugins/src/waves/waves.min.js') }}"></script>
    <script src="{{ asset('plugins/src/highlight/highlight.pack.js') }}"></script>

    <!-- THEME JS: Only include on pages that need full theme -->
    @if(request()->is('admin/dashboard') || request()->is('admin/profile'))
        <script src="{{ asset('build/assets/app-CamlIauq.js') }}"></script>
    @endif

    <!-- PAGE SPECIFIC SCRIPTS -->
    @yield('scripts')

</body>
</html>
