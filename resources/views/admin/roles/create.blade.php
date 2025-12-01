@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($role) ? 'Edit Role' : 'Add New Role' }}</h4>
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

            <form class="row g-3" action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
                @csrf
                @if(isset($role)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $role->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional, auto-generated if empty)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $role->slug ?? '') }}">
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $role->description ?? '') }}</textarea>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                            {{ (old('is_active', $role->is_active ?? true) ? 'checked' : '') }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Permissions</label>
                    <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">{{ $group ?: 'General' }}</h6>
                            <div class="row">
                                @foreach($groupPermissions as $permission)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                            {{ (isset($role) && $role->permissions->contains($permission->id)) || in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

