@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">

    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-4">

            <h4>{{ isset($barMenuItem) ? 'Edit Menu Item' : 'Add Menu Item' }}</h4>

            @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form 
                action="{{ isset($barMenuItem) 
                    ? route('admin.bar-menu-items.update', $barMenuItem->id)
                    : route('admin.bar-menu-items.store') }}"
                method="POST" enctype="multipart/form-data"
                class="mt-3">

                @csrf
                @if(isset($barMenuItem)) @method('PUT') @endif

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bar</label>
                        <select class="form-control" name="bar_id" required>
                            <option value="">Select Bar</option>
                            @foreach($bars as $bar)
                                <option value="{{ $bar->id }}"
                                    {{ old('bar_id', $barMenuItem->bar_id ?? '') == $bar->id ? 'selected' : '' }}>
                                    {{ $bar->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Menu Category</label>
                        <select name="bar_menu_category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('bar_menu_category_id', $barMenuItem->bar_menu_category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" required
                               name="name"
                               value="{{ old('name', $barMenuItem->name ?? '') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" min="0" class="form-control"
                               name="price"
                               value="{{ old('price', $barMenuItem->price ?? '') }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3"
                                  name="description">{{ old('description', $barMenuItem->description ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>

                    @if(isset($barMenuItem) && $barMenuItem->image)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Current Image</label> <br>
                        <img src="{{ asset('storage/'.$barMenuItem->image) }}"
                             style="width:120px;height:90px;object-fit:cover;border-radius:5px;">
                    </div>
                    @endif

                </div>

                <button type="submit" class="btn btn-primary">
                    {{ isset($barMenuItem) ? 'Update' : 'Create' }}
                </button>

            </form>

        </div>
    </div>

</div>
@endsection
