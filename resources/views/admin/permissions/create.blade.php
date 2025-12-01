@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($permission) ? 'Edit Permission' : 'Add New Permission' }}</h4>
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

            <form class="row g-3" action="{{ isset($permission) ? route('admin.permissions.update', $permission) : route('admin.permissions.store') }}" method="POST">
                @csrf
                @if(isset($permission)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $permission->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional, auto-generated if empty)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $permission->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Group</label>
                    <select class="form-select" name="group">
                        <option value="">Select Group</option>
                        <option value="users" {{ (old('group', $permission->group ?? '') == 'users') ? 'selected' : '' }}>Users</option>
                        <option value="bars" {{ (old('group', $permission->group ?? '') == 'bars') ? 'selected' : '' }}>Bars</option>
                        <option value="bookings" {{ (old('group', $permission->group ?? '') == 'bookings') ? 'selected' : '' }}>Bookings</option>
                        <option value="events" {{ (old('group', $permission->group ?? '') == 'events') ? 'selected' : '' }}>Events</option>
                        <option value="claims" {{ (old('group', $permission->group ?? '') == 'claims') ? 'selected' : '' }}>Claims</option>
                        <option value="sections" {{ (old('group', $permission->group ?? '') == 'sections') ? 'selected' : '' }}>Sections</option>
                        <option value="settings" {{ (old('group', $permission->group ?? '') == 'settings') ? 'selected' : '' }}>Settings</option>
                    </select>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $permission->description ?? '') }}</textarea>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($permission) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

