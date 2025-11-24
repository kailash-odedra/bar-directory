{{-- @extends('layouts.app') --}}

{{-- @section('sidebar') --}}
<div class="sidebar-wrapper sidebar-theme">

    <nav id="sidebar">

        <div class="navbar-nav theme-brand flex-row  text-center">
            <div class="nav-logo">
                <div class="nav-item theme-logo">
                    <a href="{{getRouterValue()}}dashboard/analytics">
                        <img src="{{Vite::asset('resources/images/logo2.svg')}}" class="logo-light navbar-logo-g" alt="logo">
                        <img src="{{Vite::asset('resources/images/logo.svg')}}" class="logo-dark navbar-logo-g" alt="logo">
                    </a>
                </div>
                <div class="nav-item theme-text">
                    <a href="{{getRouterValue()}}dashboard/analytics" class="nav-link"> CORK </a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="profile-info">
            <div class="user-info">
                <div class="profile-img">
                    <img src="{{Vite::asset('resources/images/profile-30.png')}}" alt="avatar">
                </div>
                <div class="profile-content">
                    <h6 class="">Shaun Park</h6>
                    <p class="">Project Leader</p>
                </div>
            </div>
        </div>
                        
        <div class="shadow-bottom"></div>
        <ul class="list-unstyled menu-categories" id="accordionExample">
            <li class="menu {{ ($catName === 'dashboard') ? 'active' : '' }}">
                <a href="#dashboard" data-bs-toggle="collapse" aria-expanded=" {{ ($catName === 'dashboard') ? 'true' : 'false' }}" class="dropdown-toggle">
                    <div class="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <span>Dashboard</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </div>
                </a>
                <ul class="collapse submenu list-unstyled {{ ($catName === 'dashboard') ? 'show' : '' }} " id="dashboard" data-bs-parent="#accordionExample">
                    <li class="{{ Request::routeIs('analytics') ? 'active' : '' }}">
                        <a href="{{getRouterValue()}}dashboard/analytics"> Analytics </a>
                    </li>
                    <li class="{{ Request::routeIs('sales') ? 'active' : '' }}">
                        <a href="{{getRouterValue()}}dashboard/sales"> Sales </a>
                    </li>
                </ul>
            </li>

            
        </ul>
        
    </nav>

</div>
{{-- @endsection --}}