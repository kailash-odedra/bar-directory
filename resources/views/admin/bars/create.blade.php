@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($bar) ? 'Edit Bar' : 'Add New Bar' }}</h4>
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

            <form class="row g-3" action="{{ isset($bar) ? route('admin.bar.update', $bar) : route('admin.bar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($bar)) @method('PUT') @endif

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
                        value="{{ old('address', $bar->location->address ?? '') }}">
                </div>

                {{-- Country --}}
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <select name="country_id" id="country" class="form-select" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ old('country_id', $bar->location->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- State --}}
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <select name="state_id" id="state" class="form-select" required>
                        <option value="">Select State</option>
                        @foreach($states as $s)
                            <option value="{{ $s->id }}"
                                {{ old('state_id', $bar->location->state_id ?? '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- City --}}
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <select name="city_id" id="city" class="form-select">
                        <option value="">Select City</option>
                        @if(isset($bar) && $bar->location && $bar->location->state_id)
                            @php
                                $cities = \App\Models\City::where('state_id', $bar->location->state_id)->where('is_active', true)->get();
                                $selectedCity = \App\Models\City::where('name', $bar->location->city)->where('state_id', $bar->location->state_id)->first();
                            @endphp
                            @foreach($cities as $cityOption)
                                <option value="{{ $cityOption->id }}" 
                                    {{ ($selectedCity && $selectedCity->id == $cityOption->id) || old('city_id') == $cityOption->id ? 'selected' : '' }}>
                                    {{ $cityOption->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="city" id="city_name" value="{{ old('city', $bar->location->city ?? '') }}">
                </div>
                {{-- Region --}}
                <div class="col-md-4">
                    <label class="form-label">Region</label>
                    <select name="region_id" id="region" class="form-select">
                        <option value="">Select Region</option>
                        @if(isset($bar) && $bar->location && $bar->location->city)
                            @php
                                $selectedCityForRegion = \App\Models\City::where('name', $bar->location->city)->first();
                                if($selectedCityForRegion) {
                                    $regions = \App\Models\Region::where('city_id', $selectedCityForRegion->id)->where('is_active', true)->get();
                                    $selectedRegion = \App\Models\Region::where('name', $bar->location->region)->where('city_id', $selectedCityForRegion->id)->first();
                                } else {
                                    $regions = collect();
                                    $selectedRegion = null;
                                }
                            @endphp
                            @foreach($regions as $regionOption)
                                <option value="{{ $regionOption->id }}" 
                                    {{ ($selectedRegion && $selectedRegion->id == $regionOption->id) || old('region_id') == $regionOption->id ? 'selected' : '' }}>
                                    {{ $regionOption->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" name="region" id="region_name" value="{{ old('region', $bar->location->region ?? '') }}">
                </div>
                {{-- ZIP --}}
                <div class="col-md-4">
                    <label class="form-label">Zip</label>
                    <input type="text" class="form-control" name="zip"
                        value="{{ old('zip', $bar->location->zipcode ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control"
                        value="{{ old('latitude', $bar->location->latitude ?? '') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control"
                        value="{{ old('longitude', $bar->location->longitude ?? '') }}">
                </div>

                {{-- Phone --}}
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone"
                        value="{{ old('phone', $bar->location->phone ?? '') }}">
                </div>

                {{-- Email --}}
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email"
                        value="{{ old('email', $bar->location->email ?? '') }}">
                </div>

                {{-- Tags --}}
                <div class="col-md-8">
                    <label class="form-label">Tags / Categories</label>
                    <select class="form-select select2" name="tags[]" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}"
                                {{ isset($bar) && $bar->tags->contains($tag->id) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>

                </div>
                {{-- Logo --}}
                
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" class="form-control" name="logo">
                    @if(isset($bar) && $bar->logo)
                        <img src="{{ asset('storage/'.$bar->logo) }}" width="80" class="mt-2">
                    @endif
                </div>

                {{-- Cover Image --}}
                <div class="col-md-6">
                    <label class="form-label">Cover Image</label>
                    <input type="file" class="form-control" name="cover_image">
                    @if(isset($bar) && $bar->cover_image)
                        <img src="{{ asset('storage/'.$bar->cover_image) }}" width="80" class="mt-2">
                    @endif
                </div>

                <div class="col-12">
                    <label class="form-label">Gallery Images (Multiple)</label>
                    <input type="file" class="form-control" name="gallery[]" multiple>
                </div>
                @if(isset($bar) && $bar->images->count())
                    <div class="col-12 mt-2">
                        @foreach($bar->images as $img)
                            <img src="{{ asset('storage/'.$img->path) }}" width="90" class="me-2 mb-2" alt="Gallery Image">
                        @endforeach
                    </div>
                @endif
                <h5 class="mt-4">Social Media Links</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Facebook</label>
                        <input type="text" name="facebook" class="form-control"
                            value="{{ old('facebook', $bar->facebook ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Instagram</label>
                        <input type="text" name="instagram" class="form-control"
                            value="{{ old('instagram', $bar->instagram ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">TikTok</label>
                        <input type="text" name="tiktok" class="form-control"
                            value="{{ old('tiktok', $bar->tiktok ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">YouTube</label>
                        <input type="text" name="youtube" class="form-control"
                            value="{{ old('youtube', $bar->youtube ?? '') }}">
                    </div>

                </div>
                
                {{-- Featured & Status --}}
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" 
                                {{ old('is_featured', isset($bar) && $bar->is_featured ? 'checked' : '') }}>
                            <label class="form-check-label" for="is_featured">
                                <strong>Featured Bar</strong> (Show on homepage)
                            </label>
                        </div>
                    </div>
                </div>

                <h4 class="mt-4">SEO Settings</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $bar->meta_title ?? '') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Meta Keywords</label>
                        <textarea name="meta_keywords" class="form-control" rows="1">{{ old('meta_keywords', $bar->meta_keywords ?? '') }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $bar->meta_description ?? '') }}</textarea>
                    </div>
                </div>
                {{-- Weekly Timings --}}
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
                            @foreach($days as $d)
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

                {{-- Submit Button --}}
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($bar) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ route('admin.bar.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
{{-- AJAX State Loader --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select Tags",
        width: '100%'
    });
});
document.addEventListener('DOMContentLoaded', function () {
    let country = document.getElementById('country');
    let state = document.getElementById('state');
    let city = document.getElementById('city');
    let region = document.getElementById('region');
    let cityNameInput = document.getElementById('city_name');
    let regionNameInput = document.getElementById('region_name');

    // Country change handler
    country.addEventListener('change', function () {
        const countryId = this.value;
        
        // Reset state dropdown
        state.innerHTML = '<option value="">Select State</option>';
        state.disabled = true;
        
        // Reset city and region
        city.innerHTML = '<option value="">Select City</option>';
        city.disabled = true;
        cityNameInput.value = '';
        
        region.innerHTML = '<option value="">Select Region</option>';
        region.disabled = true;
        regionNameInput.value = '';

        if (!countryId) {
            state.innerHTML = '<option value="">Select State</option>';
            state.disabled = false;
            return;
        }

        fetch("{{ url('admin/get-states') }}/" + countryId)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Select State</option>';
            data.forEach(function (s) {
                html += `<option value="${s.id}">${s.name}</option>`;
            });
            state.innerHTML = html;
            state.disabled = false;
        })
        .catch(error => {
            console.error('Error loading states:', error);
            state.innerHTML = '<option value="">Error loading states</option>';
            state.disabled = false;
        });
    });

    // State change handler
    state.addEventListener('change', function () {
        const stateId = this.value;
        
        // Reset city dropdown
        city.innerHTML = '<option value="">Select City</option>';
        city.disabled = true;
        cityNameInput.value = '';
        
        // Reset region
        region.innerHTML = '<option value="">Select Region</option>';
        region.disabled = true;
        regionNameInput.value = '';

        if (!stateId) {
            city.innerHTML = '<option value="">Select City</option>';
            city.disabled = false;
            return;
        }

        fetch("{{ url('admin/get-cities') }}/" + stateId)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Select City</option>';
            data.forEach(function (c) {
                html += `<option value="${c.id}" data-name="${c.name}">${c.name}</option>`;
            });
            city.innerHTML = html;
            city.disabled = false;
            
            // If there's a pre-selected city (edit mode), select it
            @if(isset($bar) && $bar->location && $bar->location->city)
                const selectedCityName = "{{ $bar->location->city }}";
                const cityOption = Array.from(city.options).find(opt => opt.text === selectedCityName);
                if (cityOption) {
                    city.value = cityOption.value;
                    cityNameInput.value = selectedCityName;
                    city.dispatchEvent(new Event('change'));
                }
            @endif
        })
        .catch(error => {
            console.error('Error loading cities:', error);
            city.innerHTML = '<option value="">Error loading cities</option>';
            city.disabled = false;
        });
    });

    // City change handler
    city.addEventListener('change', function () {
        const cityId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const cityName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.text : '';
        
        // Update city name hidden field
        cityNameInput.value = cityName;
        
        // Reset region dropdown
        region.innerHTML = '<option value="">Select Region</option>';
        region.disabled = true;
        regionNameInput.value = '';

        if (!cityId) {
            region.innerHTML = '<option value="">Select Region</option>';
            region.disabled = false;
            return;
        }

        fetch("{{ url('admin/get-regions') }}/" + cityId)
        .then(res => res.json())
        .then(data => {
            let html = '<option value="">Select Region</option>';
            data.forEach(function (r) {
                html += `<option value="${r.id}" data-name="${r.name}">${r.name}</option>`;
            });
            region.innerHTML = html;
            region.disabled = false;
            
            // If there's a pre-selected region (edit mode), select it
            @if(isset($bar) && $bar->location && $bar->location->region)
                const selectedRegionName = "{{ $bar->location->region }}";
                const regionOption = Array.from(region.options).find(opt => opt.text === selectedRegionName);
                if (regionOption) {
                    region.value = regionOption.value;
                    regionNameInput.value = selectedRegionName;
                }
            @endif
        })
        .catch(error => {
            console.error('Error loading regions:', error);
            region.innerHTML = '<option value="">Error loading regions</option>';
            region.disabled = false;
        });
    });

    // Region change handler
    region.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const regionName = selectedOption ? selectedOption.getAttribute('data-name') || selectedOption.text : '';
        
        // Update region name hidden field
        regionNameInput.value = regionName;
    });

    // Initialize dropdowns if values are pre-selected (for edit mode)
    @if(isset($bar) && $bar->location)
        // Trigger state change if country is selected
        if (country.value && state.value) {
            state.dispatchEvent(new Event('change'));
        }
    @endif
});
</script>
@endsection
