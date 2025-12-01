@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($country) ? 'Edit Country' : 'Add New Country' }}</h4>
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

            <form class="row g-3" action="{{ isset($country) ? route('admin.countries.update', $country) : route('admin.countries.store') }}" method="POST">
                @csrf
                @if(isset($country)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $country->name ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">ISO Code</label>
                    <input type="text" class="form-control" name="iso_code" value="{{ old('iso_code', $country->iso_code ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $country->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="is_active">
                        <option value="1" {{ (old('is_active', $country->is_active ?? 1) == 1) ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ (old('is_active', $country->is_active ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($country) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
