@extends('layouts.app')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($user) ? 'Edit User' : 'Add New User' }}</h4>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form class="row g-3" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($user)) @method('PUT') @endif
                
                <div class="col-12 mb-4">
                    <label class="form-label">Profile Image</label>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            @if(isset($user) && $user->image)
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
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">{{ isset($user) ? '' : '*' }}</span></label>
                    <input type="password" class="form-control" name="password" {{ isset($user) ? '' : 'required' }}>
                    @if(isset($user))
                        <small class="text-muted">Leave empty to keep current password</small>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">{{ isset($user) ? '' : '*' }}</span></label>
                    <input type="password" class="form-control" name="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Roles</label>
                    <div class="row">
                        @foreach($roles as $role)
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                                    {{ (isset($user) && $user->roles->contains($role->id)) || in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

