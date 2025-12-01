@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
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
    .widget-content input[type="password"],
    .statbox input[type="password"] {
        font-family: "Segoe UI", system-ui, -apple-system, sans-serif !important;
    }
</style>
@endsection

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>My Profile</h4>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.profile.update') }}" method="POST" class="row g-3" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-12 mb-4">
                    <label class="form-label">Profile Image</label>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            @if($user->image)
                                <img src="{{ Storage::url($user->image) }}" alt="Profile" 
                                     class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <img src="{{ Vite::asset('resources/images/profile-30.png') }}" alt="Profile" 
                                     class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ddd;">
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max size: 2MB. Allowed: JPG, PNG, GIF</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <hr>
                    <h5 class="mb-3">Change Password (Leave blank to keep current password)</h5>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                           name="current_password" value="" autocomplete="current-password">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           name="password" value="" autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" name="password_confirmation" value="" autocomplete="new-password">
                </div>

                @if($user->roles->count() > 0)
                <div class="col-12">
                    <hr>
                    <h5 class="mb-3">Assigned Roles</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <a href="{{ route('analytics') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
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

