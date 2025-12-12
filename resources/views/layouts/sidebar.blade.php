@php
use Illuminate\Support\Facades\Storage;
@endphp
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
                    <a href="{{getRouterValue()}}dashboard/analytics" class="nav-link"> Bar Directory </a>
                </div>
            </div>
            <div class="nav-item sidebar-toggle">
                <div class="btn-toggle sidebarCollapse">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                </div>
            </div>
        </div>

        @auth
        <div class="profile-info">
            <div class="user-info">
                <div class="profile-img">
                    @if(Auth::user()->image)
                        <img src="{{ Storage::url(Auth::user()->image) }}" alt="avatar">
                    @else
                        <img src="{{Vite::asset('resources/images/profile-30.png')}}" alt="avatar">
                    @endif
                </div>
                <div class="profile-content">
                    <h6 class="">{{ Auth::user()->name ?? 'Admin' }}</h6>
                    <p class="">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
                </div>
            </div>
        </div>
        @endauth
                        
        <div class="shadow-bottom"></div>
        <ul class="list-unstyled menu-categories" id="accordionExample">
            @canAny(['bar-module', 'bar-view', 'bar-create', 'bar-edit', 'bar-delete'])
            <li class="menu {{ ($catName === 'bar') ? 'active' : '' }}">
                <a href="#barMenu" data-bs-toggle="collapse"
                aria-expanded="{{ ($catName === 'bar') ? 'true' : 'false' }}"
                class="dropdown-toggle">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-layers">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                        <span>Bar Module</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <ul class="collapse submenu list-unstyled {{ ($catName === 'bar') ? 'show' : '' }}"
                    id="barMenu"
                    data-bs-parent="#accordionExample">

                    @canAny(['bar-module', 'bar-view', 'bar-create', 'bar-edit', 'bar-delete'])
                    <li class="{{ Request::is('admin/bar') && !Request::is('admin/bars/pending-approval') ? 'active' : '' }}">
                        <a href="{{ url('admin/bar') }}">All Bars</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'bar-view', 'bar-create', 'bar-edit', 'bar-delete'])
                    <li class="{{ Request::is('admin/bars/pending-approval') ? 'active' : '' }}">
                        <a href="{{ route('admin.bar.pendingApproval') }}">Pending Approvals</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'bar-tags-view', 'bar-tags-create', 'bar-tags-edit', 'bar-tags-delete'])
                    <li class="{{ Request::is('admin/bar-tags*') ? 'active' : '' }}">
                        <a href="{{ url('admin/bar-tags') }}">Tags / Categories</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'events-view', 'events-create', 'events-edit', 'events-delete'])
                    <li class="{{ Request::is('admin/events*') ? 'active' : '' }}">
                        <a href="{{ url('admin/events') }}">Events / Offers</a>
                    </li>
                    @endcanAny
                    
                    @canAny(['bar-module', 'bookings-view', 'bookings-create', 'bookings-edit', 'bookings-delete'])
                    <li class="{{ Request::is('admin/bookings*') ? 'active' : '' }}">
                        <a href="{{ url('admin/bookings') }}">Bookings</a>
                    </li>
                    @endcanAny
                    
                    @canAny(['bar-module', 'claims-view', 'claims-create', 'claims-edit', 'claims-delete'])
                    <li class="{{ Request::is('admin/claims*') ? 'active' : '' }}">
                        <a href="{{ url('admin/claims') }}">Bar Claims</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'reviews-view', 'reviews-create', 'reviews-edit', 'reviews-delete'])
                    <li class="{{ Request::is('admin/bar-reviews*') ? 'active' : '' }}">
                        <a href="{{ url('admin/bar-reviews') }}">Reviews & Ratings</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'menu-categories-view', 'menu-categories-create', 'menu-categories-edit', 'menu-categories-delete'])
                    <li class="{{ Request::is('admin/bar-menu-categories') ? 'active' : '' }}">
                        <a href="{{ url('admin/bar-menu-categories') }}">Menu Categories</a>
                    </li>
                    @endcanAny

                    @canAny(['bar-module', 'menu-items-view', 'menu-items-create', 'menu-items-edit', 'menu-items-delete'])
                    <li class="{{ Request::is('admin/bar-menu-items') ? 'active' : '' }}">
                        <a href="{{ url('admin/bar-menu-items') }}">Menu Items</a>
                    </li>
                    @endcanAny
                </ul>
            </li>
            @endcanAny

            @canAny(['geo-module', 'countries-view', 'countries-create', 'countries-edit', 'countries-delete', 'states-view', 'states-create', 'states-edit', 'states-delete', 'cities-view', 'cities-create', 'cities-edit', 'cities-delete', 'regions-view', 'regions-create', 'regions-edit', 'regions-delete'])
            <li class="menu {{ ($catName === 'geo' || Request::is('admin/countries*') || Request::is('admin/states*') || Request::is('admin/cities*') || Request::is('admin/regions*')) ? 'active' : '' }}">
                <a href="#geoMenu" data-bs-toggle="collapse"
                aria-expanded="{{ ($catName === 'geo' || Request::is('admin/countries*') || Request::is('admin/states*') || Request::is('admin/cities*') || Request::is('admin/regions*')) ? 'true' : 'false' }}"
                class="dropdown-toggle">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-map">
                            <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                            <line x1="8" y1="2" x2="8" y2="18"></line>
                            <line x1="16" y1="6" x2="16" y2="22"></line>
                        </svg>
                        <span>Location</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <ul class="collapse submenu list-unstyled {{ ($catName === 'geo' || Request::is('admin/countries*') || Request::is('admin/states*') || Request::is('admin/cities*') || Request::is('admin/regions*')) ? 'show' : '' }}"
                    id="geoMenu"
                    data-bs-parent="#accordionExample">

                    @canAny(['geo-module', 'countries-view', 'countries-create', 'countries-edit', 'countries-delete'])
                    <li class="{{ Request::is('admin/countries*') ? 'active' : '' }}">
                        <a href="{{ url('admin/countries') }}">Countries</a>
                    </li>
                    @endcanAny

                    @canAny(['geo-module', 'states-view', 'states-create', 'states-edit', 'states-delete'])
                    <li class="{{ Request::is('admin/states*') ? 'active' : '' }}">
                        <a href="{{ url('admin/states') }}">States</a>
                    </li>
                    @endcanAny

                    @canAny(['geo-module', 'cities-view', 'cities-create', 'cities-edit', 'cities-delete'])
                    <li class="{{ Request::is('admin/cities*') ? 'active' : '' }}">
                        <a href="{{ url('admin/cities') }}">Cities</a>
                    </li>
                    @endcanAny

                    @canAny(['geo-module', 'regions-view', 'regions-create', 'regions-edit', 'regions-delete'])
                    <li class="{{ Request::is('admin/regions*') ? 'active' : '' }}">
                        <a href="{{ url('admin/regions') }}">Regions</a>
                    </li>
                    @endcanAny
                </ul>
            </li>
            @endcanAny

            

            @canAny(['auth-module', 'users-view', 'users-create', 'users-edit', 'users-delete', 'roles-view', 'roles-create', 'roles-edit', 'roles-delete', 'permissions-view', 'permissions-create', 'permissions-edit', 'permissions-delete'])
            <li class="menu {{ ($catName === 'auth') ? 'active' : '' }}">
                <a href="#authMenu" data-bs-toggle="collapse"
                aria-expanded="{{ ($catName === 'auth') ? 'true' : 'false' }}"
                class="dropdown-toggle">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shield">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <span>User Roles & Auth</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <ul class="collapse submenu list-unstyled {{ ($catName === 'auth') ? 'show' : '' }}"
                    id="authMenu"
                    data-bs-parent="#accordionExample">

                    @canAny(['auth-module', 'users-view', 'users-create', 'users-edit', 'users-delete'])
                    <li class="{{ Request::is('admin/users*') ? 'active' : '' }}">
                        <a href="{{ url('admin/users') }}">Users</a>
                    </li>
                    @endcanAny

                    @canAny(['auth-module', 'roles-view', 'roles-create', 'roles-edit', 'roles-delete'])
                    <li class="{{ Request::is('admin/roles*') ? 'active' : '' }}">
                        <a href="{{ url('admin/roles') }}">Roles</a>
                    </li>
                    @endcanAny

                    @canAny(['auth-module', 'permissions-view', 'permissions-create', 'permissions-edit', 'permissions-delete'])
                    <li class="{{ Request::is('admin/permissions*') ? 'active' : '' }}">
                        <a href="{{ url('admin/permissions') }}">Permissions</a>
                    </li>
                    @endcanAny
                </ul>
            </li>
            @endcanAny
            @canAny(['cms-module', 'sections-view', 'sections-create', 'sections-edit', 'sections-delete'])
            <li class="menu {{ ($catName === 'cms') ? 'active' : '' }}">
                <a href="#cmsMenu" data-bs-toggle="collapse"
                aria-expanded="{{ ($catName === 'cms') ? 'true' : 'false' }}"
                class="dropdown-toggle">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-layout">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="3" y1="9" x2="21" y2="9"></line>
                            <line x1="9" y1="21" x2="9" y2="9"></line>
                        </svg>
                        <span>CMS / Section</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-chevron-right">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </div>
                </a>

                <ul class="collapse submenu list-unstyled {{ ($catName === 'cms') ? 'show' : '' }}"
                    id="cmsMenu"
                    data-bs-parent="#accordionExample">

                    @canAny(['cms-module', 'sections-view', 'sections-create', 'sections-edit', 'sections-delete'])
                    <li class="{{ Request::is('admin/sections*') ? 'active' : '' }}">
                        <a href="{{ url('admin/sections') }}">All Sections</a>
                    </li>
                    @endcanAny
                </ul>
            </li>
            @endcanAny

            @canAny(['dashboard-module', 'dashboard-view', 'analytics-view', 'sales-view'])
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
                    @canAny(['dashboard-module', 'dashboard-view', 'analytics-view'])
                    <li class="{{ Request::routeIs('analytics') ? 'active' : '' }}">
                        <a href="{{getRouterValue()}}dashboard/analytics"> Analytics </a>
                    </li>
                    @endcanAny
                    @canAny(['dashboard-module', 'dashboard-view', 'sales-view'])
                    <li class="{{ Request::routeIs('sales') ? 'active' : '' }}">
                        <a href="{{getRouterValue()}}dashboard/sales"> Sales </a>
                    </li>
                    @endcanAny
                </ul>
            </li>
            @endcanAny

            
        </ul>
        
    </nav>

</div>
{{-- @endsection --}}