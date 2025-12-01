@extends('layouts.app')

@section('styles')
@vite(['resources/scss/light/assets/authentication/auth-boxed.scss'])
@vite(['resources/scss/dark/assets/authentication/auth-boxed.scss'])
<style>
    /* Fix password field display - prevent X characters */
    input[type="password"] {
        font-family: "Segoe UI", "Roboto", "Helvetica Neue", Arial, sans-serif !important;
        font-size: 16px !important;
        letter-spacing: 0.1em !important;
        -webkit-text-security: disc !important;
        -moz-text-security: disc !important;
        text-security: disc !important;
        font-variant: normal !important;
        text-rendering: auto !important;
    }
    
    /* Ensure placeholder uses normal font */
    input[type="password"]::placeholder {
        font-family: "Nunito", "Segoe UI", sans-serif !important;
        letter-spacing: normal !important;
        -webkit-text-security: none !important;
        -moz-text-security: none !important;
        text-security: none !important;
    }
    
    /* Fix for error state */
    input[type="password"].is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }
    
    /* Override any theme font that might cause issues */
    .auth-container input[type="password"],
    .card-body input[type="password"] {
        font-family: "Segoe UI", system-ui, -apple-system, sans-serif !important;
    }
</style>
@endsection

@section('content')
<div class="auth-container d-flex">

    <div class="container mx-auto align-self-center">

        <div class="row">

            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                <div class="card mt-3 mb-3">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h2>Admin Sign In</h2>
                                <p>Enter your email and password to login</p>
                            </div>

                            @if(session('success'))
                            <div class="col-12">
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            @endif

                            @error('email')
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            @enderror
                            
                            @error('password')
                            <div class="col-12">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ $message }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            @enderror

                            <form method="POST" action="{{ route('admin.login.post') }}" class="row g-3">
                                @csrf

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               name="email" value="{{ old('email') }}" required autofocus>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               name="password" value="" required autocomplete="current-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <div class="form-check form-check-primary form-check-inline">
                                            <input class="form-check-input me-3" type="checkbox" id="remember" name="remember" value="1">
                                            <label class="form-check-label" for="remember">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="mb-4">
                                        <button type="submit" class="btn btn-primary w-100">SIGN IN</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix password fields to ensure proper masking
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(function(input) {
        // Force browser to use default password masking
        input.style.fontFamily = '"Segoe UI", system-ui, -apple-system, sans-serif';
        input.style.letterSpacing = '0.1em';
        
        // Clear any pre-filled values that might cause display issues
        if (input.value && input.value.length > 0) {
            input.value = '';
        }
        
        // Ensure type is password (in case something changed it)
        if (input.type !== 'password') {
            input.type = 'password';
        }
    });
});
</script>
@endsection

