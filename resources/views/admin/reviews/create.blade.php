@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($barTag) ? 'Edit Tag' : 'Add New Tag' }}</h4>
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

            <form action="{{ isset($barTag) ? route('admin.bar-tags.update', $barTag->id) : route('admin.bar-tags.store') }}" method="POST">
                @csrf
                @if(isset($barTag)) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $barTag->name ?? '') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Slug (Optional)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $barTag->slug ?? '') }}">
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($barTag) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.bar-reviews.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
