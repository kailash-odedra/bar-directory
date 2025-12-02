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

    {{-- Loader removed for performance --}}

    <!-- GLOBAL MANDATORY STYLES -->
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

    {{-- Load correct app.js --}}
    @if(request()->is('admin/dashboard') || request()->is('admin/other-page-with-sidebar'))
        <script src="{{ asset('build/assets/app-CamlIauq.js') }}"></script>
    @endif

    @if(!request()->is('admin/bar*') &&
        !request()->is('admin/bar-tags*') &&
        !request()->is('admin/bar-menu-categories*'))
        <script src="{{ asset('build/assets/app.js') }}"></script>
    @endif

    <style>
        body:not(.dark) .logo-light { display: block; }
        body:not(.dark) .logo-dark { display: none; }
        body.dark .logo-light { display: none; }
        body.dark .logo-dark { display: block; }
        
        /* Remove bounce animation from sidebar collapse menus - COMPLETE OVERRIDE */
        #sidebar .collapse,
        #sidebar .submenu,
        #sidebar .collapse.show,
        #sidebar .submenu.show,
        #sidebar .collapse.collapsing,
        #sidebar .submenu.collapsing,
        #sidebar ul.collapse,
        #sidebar ul.submenu,
        #sidebar .sidebar-wrapper .collapse,
        #sidebar .sidebar-wrapper .submenu,
        #sidebar .menu .collapse,
        #sidebar .menu .submenu,
        #sidebar [class*="collapse"] {
            transition: none !important;
            animation: none !important;
            -webkit-transition: none !important;
            -webkit-animation: none !important;
            -moz-transition: none !important;
            -moz-animation: none !important;
            -o-transition: none !important;
            -o-animation: none !important;
            transition-duration: 0s !important;
            -webkit-transition-duration: 0s !important;
            -moz-transition-duration: 0s !important;
            -o-transition-duration: 0s !important;
            transition-property: none !important;
            -webkit-transition-property: none !important;
            -moz-transition-property: none !important;
            -o-transition-property: none !important;
            height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            will-change: auto !important;
        }
        
        /* Remove any transform/translate animations */
        #sidebar .collapse,
        #sidebar .submenu {
            transform: none !important;
            -webkit-transform: none !important;
            -moz-transform: none !important;
            -o-transform: none !important;
        }
        
        /* Instant show/hide without animation */
        #sidebar .collapse:not(.show) {
            display: none !important;
            height: auto !important;
            max-height: none !important;
            visibility: hidden !important;
        }
        
        #sidebar .collapse.show {
            display: block !important;
            height: auto !important;
            max-height: none !important;
            visibility: visible !important;
        }
        
        /* Prevent Bootstrap's collapsing class from animating */
        #sidebar .collapse.collapsing {
            transition: none !important;
            height: auto !important;
            max-height: none !important;
            display: block !important;
            visibility: visible !important;
        }
        
        /* Override Bootstrap's default collapse styles */
        #sidebar * {
            --bs-collapse-transition-time: 0s !important;
        }
        
        /* Prevent any height calculations */
        #sidebar .collapse {
            height: auto !important;
        }
    </style>
    
    <!-- Force dark mode as default - runs immediately -->
    <script>
        // ALWAYS force dark mode - override any existing preference
        (function() {
            // Always set dark mode regardless of stored preference
            const defaultTheme = {
                admin: 'Cork Admin Template',
                settings: {
                    layout: {
                        name: 'Modern Dark Menu',
                        toggle: true,
                        darkMode: true, // Always true
                        boxed: true,
                        logo: {
                            darkLogo: '../src/assets/img/logo.svg',
                            lightLogo: '../src/assets/img/logo2.svg'
                        }
                    }
                },
                reset: false
            };
            sessionStorage.setItem("theme", JSON.stringify(defaultTheme));
            
            // Add dark class immediately
            if (document.documentElement) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @isset($scrollspy)
        @if ($scrollspy)
            @vite(['resources/scss/light/assets/scrollspyNav.scss'])
            @vite(['resources/scss/dark/assets/scrollspyNav.scss'])
        @endif
    @endisset

    @yield('styles')
</head>

<body class="dark
    {{ Request::routeIs('error404') ? 'error text-center' : '' }}
    {{ Request::routeIs('maintenance') ? 'maintanence text-center' : '' }}

    {{ Request::routeIs('boxedSignIn') ||
       Request::routeIs('boxedSignUp') ||
       Request::routeIs('boxedLockscreen') ||
       Request::routeIs('boxedPasswordReset') ||
       Request::routeIs('boxed2sv') ||
       Request::routeIs('admin.login')
       ? 'form' : '' }}

    {{ Request::routeIs('coverSignIn') ||
       Request::routeIs('coverSignUp') ||
       Request::routeIs('coverLockscreen') ||
       Request::routeIs('coverPasswordReset') ||
       Request::routeIs('cover2sv')
       ? 'form' : '' }}

    {{ Request::routeIs('collapsed') ? 'alt-menu' : '' }}
" layout="{{ Request::routeIs('full-width') ? 'full-width' : 'boxed' }}">

    {{-- Loader removed for performance --}}
    <div id="overlay"></div>
    <div class="overlay"></div>
    <div class="search-overlay"></div>

    @isset($simplePage)
        @if ($simplePage)
            @yield('content')
        @else

        @if (!Request::routeIs('blank'))
            @include('layouts.navbar')
        @endif

        <div class="main-container" id="container">
            <div class="overlay"></div>
            <div class="search-overlay"></div>

            @if (!Request::routeIs('blank'))
                @include('layouts.sidebar')
            @endif

            <div id="content" class="main-content {{ Request::routeIs('blank') ? 'ms-0 mt-0' : '' }}">
                @isset($scrollspy)
                    @if ($scrollspy)
                        <div class="container">
                            <div class="middle-content container-xxl p-0">
                                @yield('content')
                            </div>
                        </div>
                    @else
                        <div class="layout-px-spacing">
                            <div class="middle-content {{ Request::routeIs('boxed') ? 'container-xxl' : '' }} p-0">
                                <div style="display:none" class="toggle-code-snippet"></div>
                                @yield('content')
                            </div>
                        </div>
                    @endif
                @endisset

                @if (!Request::routeIs('blank'))
                    @include('layouts.footer')
                @endif
            </div>
        </div>
        @endif
    @endisset

    <!-- GLOBAL SCRIPTS -->
    <script src="{{ asset('plugins/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('plugins/src/mousetrap/mousetrap.min.js') }}"></script>
    <script src="{{ asset('plugins/src/waves/waves.min.js') }}"></script>
    <script src="{{ asset('plugins/src/highlight/highlight.pack.js') }}"></script>

            @vite(['resources/layouts/modern-dark-menu/app.js'])

            @isset($scrollspy)
                @if ($scrollspy)
                    @vite(['resources/js/scrollspyNav.js'])
                @endif
            @endisset

            @yield('scripts')

<!-- Override Bootstrap Collapse animation for sidebar - INSTANT TOGGLE -->
<script>
(function() {
    // Completely replace Bootstrap collapse with instant toggle for sidebar
    function replaceSidebarCollapse() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        
        // Find all collapse toggles in sidebar
        const collapseToggles = sidebar.querySelectorAll('[data-bs-toggle="collapse"]');
        
        collapseToggles.forEach(function(toggle) {
            // Remove Bootstrap's data attribute to prevent initialization
            toggle.removeAttribute('data-bs-toggle');
            
            // Get target element
            const targetId = toggle.getAttribute('href') || toggle.getAttribute('data-bs-target');
            if (!targetId) return;
            
            const target = document.querySelector(targetId);
            if (!target) return;
            
            // Destroy any existing Bootstrap collapse instance
            if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                const collapseInstance = bootstrap.Collapse.getInstance(target);
                if (collapseInstance) {
                    collapseInstance.dispose();
                }
            }
            
            // Remove all transition/animation styles
            target.style.transition = 'none';
            target.style.transitionDuration = '0s';
            target.style.animation = 'none';
            target.style.height = 'auto';
            
            // Add custom click handler for instant toggle
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle show class instantly
                if (target.classList.contains('show')) {
                    target.classList.remove('show');
                    target.style.display = 'none';
                    toggle.setAttribute('aria-expanded', 'false');
                } else {
                    target.classList.add('show');
                    target.style.display = 'block';
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });
        
        // Ensure all sidebar collapse elements have no transitions
        const collapseElements = sidebar.querySelectorAll('.collapse');
        collapseElements.forEach(function(el) {
            el.style.transition = 'none';
            el.style.transitionDuration = '0s';
            el.style.animation = 'none';
            el.style.height = 'auto';
            
            // Set initial display state
            if (el.classList.contains('show')) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        });
    }
    
    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', replaceSidebarCollapse);
    } else {
        replaceSidebarCollapse();
    }
    
    // Also run after everything loads
    window.addEventListener('load', replaceSidebarCollapse);
    
    // Run after a short delay to ensure Bootstrap hasn't initialized yet
    setTimeout(replaceSidebarCollapse, 50);
})();
</script>


<!-- 🔥 FINAL FIX: Prevent CORK JS from crashing -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    
    // Ensure dark mode is set and applied
    const storedTheme = sessionStorage.getItem("theme");
    let themeObj = null;
    
    if (!storedTheme) {
        // No theme stored - set dark mode as default
        themeObj = {
            admin: 'Cork Admin Template',
            settings: {
                layout: {
                    name: 'Modern Dark Menu',
                    toggle: true,
                    darkMode: true, // Dark mode as default
                    boxed: true,
                    logo: {
                        darkLogo: '../src/assets/img/logo.svg',
                        lightLogo: '../src/assets/img/logo2.svg'
                    }
                }
            },
            reset: false
        };
        sessionStorage.setItem("theme", JSON.stringify(themeObj));
        document.body.classList.add('dark');
        document.documentElement.classList.add('dark');
    } else {
        // Theme exists - ensure dark mode is set
        try {
            themeObj = JSON.parse(storedTheme);
            if (themeObj.settings && themeObj.settings.layout) {
                // Force dark mode
                themeObj.settings.layout.darkMode = true;
                sessionStorage.setItem("theme", JSON.stringify(themeObj));
                document.body.classList.add('dark');
                document.documentElement.classList.add('dark');
            }
        } catch (e) {
            console.warn("Error parsing theme:", e);
            // Reset to dark mode on error
            const defaultTheme = {
                admin: 'Cork Admin Template',
                settings: {
                    layout: {
                        name: 'Modern Dark Menu',
                        toggle: true,
                        darkMode: true,
                        boxed: true,
                        logo: {
                            darkLogo: '../src/assets/img/logo.svg',
                            lightLogo: '../src/assets/img/logo2.svg'
                        }
                    }
                },
                reset: false
            };
            sessionStorage.setItem("theme", JSON.stringify(defaultTheme));
            document.body.classList.add('dark');
            document.documentElement.classList.add('dark');
        }
    }

    const hasToggle = document.getElementById('theme-toggle');
    const hasSwitcher = document.getElementById('darkModeSwitcher');

    if (window.app) {

        // Safe theme toggle
        if (typeof window.app.themeToggle === "function") {
            const orig = window.app.themeToggle;
            window.app.themeToggle = function () {
                if (!hasToggle || !hasSwitcher) return;
                orig();
            };
        }

        // Safe overlay
        if (typeof window.app.overlay === "function") {
            const orig = window.app.overlay;
            window.app.overlay = function () {
                const overlay = document.querySelector('.search-overlay');
                if (!overlay) return;
                orig();
            };
        }

        // Safe init
        if (typeof window.app.init === "function") {
            try { window.app.init(); }
            catch (e) { console.warn("CORK INIT BLOCKED:", e); }
        }
    }
    
    // Force dark mode after all scripts load
    setTimeout(function() {
        const theme = sessionStorage.getItem("theme");
        if (theme) {
            try {
                const themeObj = JSON.parse(theme);
                if (themeObj.settings && themeObj.settings.layout) {
                    themeObj.settings.layout.darkMode = true;
                    sessionStorage.setItem("theme", JSON.stringify(themeObj));
                }
            } catch(e) {}
        }
        document.body.classList.add('dark');
        document.documentElement.classList.add('dark');
    }, 100);
    
    // Disable bounce animation on sidebar collapse menus
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        // Override Bootstrap collapse behavior for sidebar menus
        const collapseElements = sidebar.querySelectorAll('.collapse');
        collapseElements.forEach(function(collapseEl) {
            // Remove transition duration
            collapseEl.style.transitionDuration = '0s';
            
            // Listen for collapse events and make them instant
            collapseEl.addEventListener('show.bs.collapse', function(e) {
                this.style.transitionDuration = '0s';
                this.style.height = 'auto';
            });
            
            collapseEl.addEventListener('shown.bs.collapse', function(e) {
                this.style.transitionDuration = '0s';
                this.style.height = 'auto';
            });
            
            collapseEl.addEventListener('hide.bs.collapse', function(e) {
                this.style.transitionDuration = '0s';
            });
            
            collapseEl.addEventListener('hidden.bs.collapse', function(e) {
                this.style.transitionDuration = '0s';
            });
        });
        
        // Override all collapse toggles in sidebar
        const collapseToggles = sidebar.querySelectorAll('[data-bs-toggle="collapse"]');
        collapseToggles.forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href') || this.getAttribute('data-bs-target');
                if (targetId) {
                    const target = document.querySelector(targetId);
                    if (target && target.classList.contains('collapse')) {
                        // Force instant transition
                        target.style.transitionDuration = '0s';
                        setTimeout(function() {
                            if (target.classList.contains('show')) {
                                target.style.height = 'auto';
                            }
                        }, 0);
                    }
                }
            });
        });
    }
});

// Also run on window load to ensure dark mode is applied
window.addEventListener('load', function() {
    // Force dark mode
    const theme = sessionStorage.getItem("theme");
    if (theme) {
        try {
            const themeObj = JSON.parse(theme);
            if (themeObj.settings && themeObj.settings.layout) {
                themeObj.settings.layout.darkMode = true;
                sessionStorage.setItem("theme", JSON.stringify(themeObj));
            }
        } catch(e) {}
    }
    document.body.classList.add('dark');
    document.documentElement.classList.add('dark');
    
    // Disable bounce animation on sidebar collapse menus (run after Bootstrap loads)
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        // Override Bootstrap collapse transition duration
        const collapseElements = sidebar.querySelectorAll('.collapse');
        collapseElements.forEach(function(collapseEl) {
            collapseEl.style.transitionDuration = '0s';
            collapseEl.style.transition = 'none';
        });
        
        // Override Bootstrap Collapse class for sidebar elements
        if (window.bootstrap && window.bootstrap.Collapse) {
            const collapseToggles = sidebar.querySelectorAll('[data-bs-toggle="collapse"]');
            collapseToggles.forEach(function(toggle) {
                const targetId = toggle.getAttribute('href') || toggle.getAttribute('data-bs-target');
                if (targetId) {
                    const target = document.querySelector(targetId);
                    if (target) {
                        // Get or create Collapse instance
                        let collapseInstance = bootstrap.Collapse.getInstance(target);
                        if (!collapseInstance) {
                            collapseInstance = new bootstrap.Collapse(target, {
                                toggle: false
                            });
                        }
                        
                        // Override the _setTransitionDuration method
                        if (collapseInstance._setTransitionDuration) {
                            const originalSetDuration = collapseInstance._setTransitionDuration.bind(collapseInstance);
                            collapseInstance._setTransitionDuration = function() {
                                // Force 0 duration
                                this._element.style.transitionDuration = '0s';
                            };
                        }
                    }
                }
            });
        }
    }
});
</script>

</body>
</html>
