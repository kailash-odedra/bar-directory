@extends('layouts.app')
@if(request()->is('admin/dashboard') || request()->is('admin/other-page-with-sidebar'))
    <script src="{{ asset('build/assets/app-CamlIauq.js') }}"></script>
@endif

@section('styles')
    @vite([
        'resources/scss/light/assets/components/timeline.scss',
        'resources/scss/light/plugins/table/datatable/dt-global_style.scss',
        'resources/scss/light/plugins/table/datatable/custom_dt_miscellaneous.scss',
        'resources/scss/dark/plugins/table/datatable/dt-global_style.scss',
        'resources/scss/dark/plugins/table/datatable/custom_dt_miscellaneous.scss'
    ])
@endsection

@section('title','Add Bar')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">

        <div id="flBarForm" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12">
                            <h4>{{ isset($bar) ? 'Edit Bar' : 'Add New Bar' }}</h4>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area">

                    {{-- FORM START --}}
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
                            <label class="form-label">Bar Name</label>
                            <input type="text" class="form-control" name="name"
                                value="{{ old('name', $bar->name ?? '') }}" required>
                        </div>

                        {{-- Slug --}}
                        <div class="col-md-6">
                            <label class="form-label">Slug / URL</label>
                            <input type="text" class="form-control" name="slug"
                                value="{{ old('slug', $bar->slug ?? '') }}">
                        </div>

                        {{-- Short Description --}}
                        <div class="col-12">
                            <label class="form-label">Short Description</label>
                            <input type="text" class="form-control" name="short_description"
                                value="{{ old('short_description', $bar->short_description ?? '') }}">
                        </div>

                        {{-- Full Description --}}
                        <div class="col-12">
                            <label class="form-label">Full Description</label>
                            <textarea class="form-control" name="full_description" rows="4">{{ old('full_description', $bar->full_description ?? '') }}</textarea>
                        </div>

                        {{-- Address --}}
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address"
                                value="{{ old('address', $bar->address ?? '') }}">
                        </div>

                        {{-- COUNTRY --}}
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <select name="country_id" id="country" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}"
                                        @if(isset($bar) && $bar->country_id == $country->id) selected @endif>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- STATE --}}
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <select name="state_id" id="state" class="form-select" required>
                                @if(isset($states))
                                    @foreach($states as $s)
                                        <option value="{{ $s->id }}"
                                            @if(isset($bar) && $bar->state_id == $s->id) selected @endif>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Select State</option>
                                @endif
                            </select>
                        </div>

                        {{-- City --}}
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city"
                                value="{{ old('city', $bar->city ?? '') }}">
                        </div>

                        {{-- ZIP --}}
                        <div class="col-md-3">
                            <label class="form-label">Zip</label>
                            <input type="text" class="form-control" name="zip"
                                value="{{ old('zip', $bar->zip ?? '') }}">
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-4">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                value="{{ old('phone', $bar->phone ?? '') }}">
                        </div>

                        {{-- Email --}}
                        <div class="col-md-5">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="{{ old('email', $bar->email ?? '') }}">
                        </div>

                        {{-- TAGS --}}
                        <div class="col-md-12">
                            <label class="form-label">Tags / Categories</label>
                            <select class="form-select" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        {{ isset($bar) && $bar->tags->contains($tag->id) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- LOGO --}}
                        <div class="col-md-6">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo">
                            @if(isset($bar) && $bar->logo)
                                <img src="{{ asset('storage/'.$bar->logo) }}" width="80" class="mt-2">
                            @endif
                        </div>

                        {{-- COVER IMAGE --}}
                        <div class="col-md-6">
                            <label class="form-label">Cover Image</label>
                            <input type="file" class="form-control" name="cover_image">
                            @if(isset($bar) && $bar->cover_image)
                                <img src="{{ asset('storage/'.$bar->cover_image) }}" width="80" class="mt-2">
                            @endif
                        </div>

                        {{-- GALLERY MULTI IMAGES --}}
                        <div class="col-12">
                            <label class="form-label">Gallery Images (Multiple)</label>
                            <input type="file" class="form-control" name="images[]" multiple>
                        </div>

                        @if(isset($bar))
                        <div class="col-12 mt-2">
                            @foreach($bar->images as $img)
                                <img src="{{ asset('storage/'.$img->image) }}" width="90" class="me-2 mb-2">
                            @endforeach
                        </div>
                        @endif

                        {{-- WEEKLY TIMINGS --}}
                        <div class="col-12 mt-4">
                            <h5>Weekly Timings</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Open</th>
                                        <th>Close</th>
                                        <th>Closed?</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @php
                                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                    @endphp

                                    @foreach($days as $i => $d)
                                        @php
                                            $t = isset($bar) ? $bar->timings->where('weekday',$d)->first() : null;
                                        @endphp

                                        <tr>
                                            <td>
                                                {{ $d }}
                                                <input type="hidden" name="timing[{{ $d }}][day]" value="{{ $d }}">
                                            </td>

                                            <td>
                                                <input type="time" class="form-control"
                                                    name="timing[{{ $d }}][open_time]"
                                                    value="{{ $t->open_time ?? '' }}">
                                            </td>

                                            <td>
                                                <input type="time" class="form-control"
                                                    name="timing[{{ $d }}][close_time]"
                                                    value="{{ $t->close_time ?? '' }}">
                                            </td>

                                            <td class="text-center">
                                                <input type="checkbox"
                                                    name="timing[{{ $d }}][is_closed]"
                                                    {{ isset($t) && $t->is_closed ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                        {{-- BUTTON --}}
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($bar) ? 'Update Bar' : 'Create Bar' }}
                            </button>
                        </div>

                    </form>
                    {{-- FORM END --}}

                </div>
            </div>
        </div>

    </div>
</div>


{{-- AJAX STATE LOADER --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    let country = document.getElementById('country');
    let state = document.getElementById('state');

    country.addEventListener('change', function () {

        state.innerHTML = '<option>Loading...</option>';

        fetch("{{ url('admin/get-states') }}/" + this.value)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Select State</option>';
            data.forEach(function (s) {
                html += `<option value="${s.id}">${s.name}</option>`;
            });
            state.innerHTML = html;
        });

    });

});
</script>

@endsection
