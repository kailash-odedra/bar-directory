@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($city) ? 'Edit City' : 'Add New City' }}</h4>
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

            <form class="row g-3" action="{{ isset($city) ? route('admin.cities.update', $city) : route('admin.cities.store') }}" method="POST">
                @csrf
                @if(isset($city)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <select class="form-select" name="state_id" required>
                        <option value="">Select State</option>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ (isset($city) && $city->state_id == $state->id) || old('state_id') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }} ({{ $state->country->name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $city->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $city->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ (old('is_active', $city->is_active ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (old('is_active', $city->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($city) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
