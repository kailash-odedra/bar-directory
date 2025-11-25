@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">

            <div class="widget-header">
                <h4>{{ isset($menuCategory) ? 'Edit Menu Category' : 'Add New Menu Category' }}</h4>
            </div>

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form 
                action="{{ isset($menuCategory) 
                    ? url('admin/bar-menu-categories/'.$menuCategory->id) 
                    : url('admin/bar-menu-categories') }}"
                method="POST">

                @csrf
                @if(isset($menuCategory)) @method('PUT') @endif

                {{-- Select Bar --}}
                <div class="mb-3">
                    <label class="form-label">Select Bar</label>
                    <select name="bar_id" class="form-control" required>
                        <option value="">Choose Bar</option>
                        @foreach($bars as $bar)
                            <option value="{{ $bar->id }}"
                                {{ old('bar_id', $menuCategory->bar_id ?? '') == $bar->id ? 'selected' : '' }}>
                                {{ $bar->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category Name --}}
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" class="form-control" name="name"
                        value="{{ old('name', $menuCategory->name ?? '') }}" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($menuCategory) ? 'Update' : 'Create' }}
                </button>

            </form>
        </div>
    </div>
</div>
@endsection
