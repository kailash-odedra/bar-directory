@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($state) ? 'Edit State' : 'Add New State' }}</h4>
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

            <form class="row g-3" action="{{ isset($state) ? route('admin.states.update', $state) : route('admin.states.store') }}" method="POST">
                @csrf
                @if(isset($state)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">Country</label>
                    <select class="form-select" name="country_id" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ (isset($state) && $state->country_id == $country->id) || old('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $state->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $state->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ (old('is_active', $state->is_active ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (old('is_active', $state->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($state) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.states.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
