@extends('layouts.app')
@if(request()->is('admin/dashboard') || request()->is('admin/other-page-with-sidebar'))
    <script src="{{ asset('build/assets/app-CamlIauq.js') }}"></script>
@endif

@section('styles')
{{-- Style Here --}}
    <!--  BEGIN CUSTOM STYLE FILE  -->
    @vite(['resources/scss/light/assets/components/timeline.scss',
        'resources/scss/light/plugins/table/datatable/dt-global_style.scss',
        'resources/scss/light/plugins/table/datatable/custom_dt_miscellaneous.scss',
        'resources/scss/dark/plugins/table/datatable/dt-global_style.scss',
        'resources/scss/dark/plugins/table/datatable/custom_dt_miscellaneous.scss'
    ])
    
    <style>
        .toggle-code-snippet { margin-bottom: 0px; }
        body.dark .toggle-code-snippet { margin-bottom: 0px; }
    </style>
@endsection
@section('title','Add Bar')
@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
           <div id="flBarForm" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ isset($bar) ? 'Edit Bar' : 'Add New Bar' }}</h4>
                        </div>                                                                        
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    {{-- Form: Create / Update --}}
                    <form class="row g-3"
                        action="{{ isset($bar) ? route('admin.bar.update', $bar->id) : route('admin.bar.store') }}"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @if(isset($bar))
                            @method('PUT')
                        @endif

                        {{-- Bar Name --}}
                        <div class="col-md-6">
                            <label for="barName" class="form-label">Bar Name</label>
                            <input type="text" class="form-control" id="barName" name="name" 
                                value="{{ old('name', $bar->name ?? '') }}" required>
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-6">
                            <label for="barSlug" class="form-label">Slug / URL</label>
                            <input type="text" class="form-control" id="barSlug" name="slug" 
                                value="{{ old('slug', $bar->slug ?? '') }}">
                        </div>

                        {{-- Short Description --}}
                        <div class="col-12">
                            <label for="shortDescription" class="form-label">Short Description</label>
                            <input type="text" class="form-control" id="shortDescription" name="short_description"
                                value="{{ old('short_description', $bar->short_description ?? '') }}">
                        </div>

                        {{-- Full Description --}}
                        <div class="col-12">
                            <label for="fullDescription" class="form-label">Full Description</label>
                            <textarea class="form-control" id="fullDescription" name="full_description" rows="4">{{ old('full_description', $bar->full_description ?? '') }}</textarea>
                        </div>

                        {{-- Address --}}
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $bar->address ?? '') }}">
                        </div>

                        {{-- City / State / Zip --}}
                        <div class="col-md-6">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ old('city', $bar->city ?? '') }}">
                        </div>

                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state"
                                value="{{ old('state', $bar->state ?? '') }}">
                        </div>

                        <div class="col-md-2">
                            <label for="zip" class="form-label">Zip</label>
                            <input type="text" class="form-control" id="zip" name="zip"
                                value="{{ old('zip', $bar->zip ?? '') }}">
                        </div>

                        {{-- Contact Info --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $bar->phone ?? '') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $bar->email ?? '') }}">
                        </div>

                        {{-- Tags / Categories --}}
                        <div class="col-md-12">
                            <label for="tags" class="form-label">Tags / Categories</label>
                            <select id="tags" class="form-select" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ isset($bar) && $bar->tags->contains($tag->id) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Images --}}
                        <div class="col-md-6">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            @if(isset($bar) && $bar->logo)
                                <img src="{{ asset('storage/'.$bar->logo) }}" alt="logo" class="img-fluid mt-2" width="80">
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image">
                            @if(isset($bar) && $bar->cover_image)
                                <img src="{{ asset('storage/'.$bar->cover_image) }}" alt="cover" class="img-fluid mt-2" width="80">
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($bar) ? 'Update Bar' : 'Add Bar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Check if the themeToggle element exists
    let themeToggle = document.querySelector('#themeToggle');
    
    if (!themeToggle) {
      // Create a hidden placeholder element that prevents JS error
      themeToggle = document.createElement('button');
      themeToggle.id = 'themeToggle';
      themeToggle.style.display = 'none';
      document.body.appendChild(themeToggle);
      console.log('Placeholder #themeToggle added to fix JS error');
    }
    
    // Similarly, if there are other elements causing issues, create them here...
  });
</script>
@endsection