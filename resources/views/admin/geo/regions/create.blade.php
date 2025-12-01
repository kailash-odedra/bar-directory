@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($region) ? 'Edit Region' : 'Add New Region' }}</h4>
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

            <form class="row g-3" action="{{ isset($region) ? route('admin.regions.update', $region) : route('admin.regions.store') }}" method="POST">
                @csrf
                @if(isset($region)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">City</label>
                    <select class="form-select" name="city_id" required>
                        <option value="">Select City</option>
                        @foreach($cities as $cityOption)
                        <option value="{{ $cityOption->id }}" {{ (isset($region) && $region->city_id == $cityOption->id) || old('city_id') == $cityOption->id ? 'selected' : '' }}>
                            {{ $cityOption->name }} ({{ $cityOption->state->name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $region->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $region->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ (old('is_active', $region->is_active ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (old('is_active', $region->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($region) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
