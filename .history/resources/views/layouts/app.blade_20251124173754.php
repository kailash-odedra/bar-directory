<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>
        @isset($title)
            @if ($title !== '')
                {{$title}} | Multipurpose Bootstrap Dashboard Template
            @else
                CORK Admin | Multipurpose Bootstrap Dashboard Template
            @endif
        @endisset
    </title>
    <link rel="icon" type="image/x-icon" href="{{Vite::asset('resources/images/favicon.ico')}}"/>
    @vite(['resources/scss/layouts/modern-dark-menu/light/loader.scss'])
    @vite(['resources/scss/layouts/modern-dark-menu/dark/loader.scss'])
    @vite(['resources/layouts/modern-dark-menu/loader.js'])

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/src/bootstrap/css/bootstrap.min.css')}}">
    @vite(['resources/scss/light/assets/main.scss'])
    @vite(['resources/scss/dark/assets/main.scss'])
    @vite(['resources/scss/light/plugins/perfect-scrollbar/perfect-scrollbar.scss'])
    @vite(['resources/scss/dark/plugins/perfect-scrollbar/perfect-scrollbar.scss'])
    <link rel="stylesheet" href="{{asset('plugins/src/waves/waves.min.css')}}">
    @vite(['resources/scss/layouts/modern-dark-menu/light/structure.scss'])
    @vite(['resources/scss/layouts/modern-dark-menu/dark/structure.scss'])
    <link rel="stylesheet" href="{{asset('plugins/src/highlight/styles/monokai-sublime.css')}}">
    @if(request()->is('admin/dashboard') || request()->is('admin/other-page-with-sidebar'))
        <script src="{{ asset('build/assets/app-CamlIauq.js') }}"></script>
    @endif
    @if (!request()->is('admin/bar*') &&
        !request()->is('admin/bar-tags*') &&
        !request()->is('admin/bar-menu-categories*'))
        <script src="{{ asset('build/assets/app.js') }}"></script>
    @endif

    <script>
        window.hasThemeToggle = document.getElementById('theme-toggle') !== null;
    </script>
    <style>
        body:not(.dark) .logo-light {
            display: block;
        }
        body:not(.dark) .logo-dark {
            display: none;
        }
        body.dark .logo-light {
            display: none;
        }
        body.dark .logo-dark {
            display: block;
        }
    </style>
   

    @isset($scrollspy)
        @if ($scrollspy)
            @vite(['resources/scss/light/assets/scrollspyNav.scss'])
            @vite(['resources/scss/dark/assets/scrollspyNav.scss'])
        @endif
    @endisset
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    @yield('styles')
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

</head>
<body class="
    {{ Request::routeIs('error404') ? 'error text-center' : '' }}
    {{ Request::routeIs('maintenance') ? 'maintanence text-center' : '' }}
    {{ 
        (Request::routeIs('boxedSignIn') || 
        Request::routeIs('boxedSignUp') || 
        Request::routeIs('boxedLockscreen') || 
        Request::routeIs('boxedPasswordReset') || 
        Request::routeIs('boxed2sv')) ? 'form' : '' 
    }}

    {{ 
        (Request::routeIs('coverSignIn') || 
        Request::routeIs('coverSignUp') || 
        Request::routeIs('coverLockscreen') || 
        Request::routeIs('coverPasswordReset') || 
        Request::routeIs('cover2sv')) ? 'form' : '' 
    }}
    {{ Request::routeIs('collapsed') ? 'alt-menu' : '' }}
    
    
" layout="{{ Request::routeIs('full-width') ? 'full-width' : 'boxed' }}" >
    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->
    <div id="overlay"></div>
    <div class="overlay"></div>
    <div class="search-overlay"></div>
    @isset($simplePage)

        @if ($simplePage)

            @yield('content')
            
        @else

        @if (!Request::routeIs('blank'))
            <!--  BEGIN NAVBAR  -->
            @include('layouts.navbar')
            <!--  END NAVBAR  -->
        @endif

            <!--  BEGIN MAIN CONTAINER  -->
            <div class="main-container" id="container">

                <div class="overlay"></div>
                <div class="search-overlay"></div>

                @if (!Request::routeIs('blank'))
                    <!--  BEGIN SIDEBAR  -->
                    @include('layouts.sidebar')
                    <!--  END SIDEBAR  -->                    
                @endif

                <!--  BEGIN CONTENT AREA  -->
                <div id="content" class="main-content {{ Request::routeIs('blank') ? 'ms-0 mt-0' : '' }}">

                    @isset($scrollspy)
                        
                        @if ($scrollspy)
                            <div class="container">
                                <div class="container">                
                                    <div class="middle-content container-xxl p-0">
            
                                        <!--  BEGIN BREADCRUMBS  -->
                                        {{-- @include('layouts.secondaryNav') --}}
                                        <!--  END BREADCRUMBS  -->
                                        
                                        <!--  BEGIN CONTENT  -->
                                        @yield('content')
                                        <!--  END CONTENT  -->

                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="layout-px-spacing">
                                <div class="middle-content {{ Request::routeIs('boxed') ? 'container-xxl' : '' }} p-0">

                                    @if (!Request::routeIs('blank'))
                                        {{-- @include('layouts.secondaryNav') --}}
                                    @endif

                                    {{-- toggle snippet div is okay to leave --}}
                                    <div class="toggle-code-snippet" style="display:none"></div>


                                    @yield('content')

                                </div>
                            </div>

                        @endif

                    @endisset
                    
                    @if (!Request::routeIs('blank'))
                        <!--  BEGIN FOOTER  -->
                        @include('layouts.footer')
                        <!--  END FOOTER  -->
                    @endif
                </div>
                <!--  END CONTENT AREA  -->

            </div>
            <!-- END MAIN CONTAINER -->
            
        @endif
        
    @endisset

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{asset('plugins/src/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('plugins/src/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('plugins/src/mousetrap/mousetrap.min.js')}}"></script>
    <script src="{{asset('plugins/src/waves/waves.min.js')}}"></script>
    <script src="{{asset('plugins/src/highlight/highlight.pack.js')}}"></script>
    @vite(['resources/layouts/modern-dark-menu/app.js'])
    
    @isset($scrollspy)
        @if ($scrollspy)
            @vite(['resources/js/scrollspyNav.js'])
        @endif
    @endisset
    
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    @yield('scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
<!-- FIX CORK THEME JS ERRORS -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    function safeQuery(selector) {
        return document.querySelector(selector);
    }

    // Patch themeToggle
    if (window.app && typeof window.app.themeToggle === "function") {
        const orig = window.app.themeToggle;

        window.app.themeToggle = function() {
            if (!safeQuery("#theme-toggle")) return;
            if (!safeQuery("#darkModeSwitcher")) return;
            orig();
        };
    }

    // Patch search overlay
    if (window.app && typeof window.app.overlay === "function") {
        const orig = window.app.overlay;

        window.app.overlay = function() {
            if (!safeQuery(".search-overlay") && !safeQuery(".searchOverlay")) return;
            orig();
        };
    }

    // Patch init()
    if (window.app && typeof window.app.init === "function") {
        try { window.app.init(); } catch (e) {
            console.warn("Cork init blocked:", e);
        }
    }

});
</script>

</body>
</html>