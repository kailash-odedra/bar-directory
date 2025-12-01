<div class="mb-3">
    <label class="form-label">City</label>
    <select name="city_id" class="form-select" required>
        <option value="">Select city</option>
        @foreach($cities as $cityOption)
            <option value="{{ $cityOption->id }}" @selected(old('city_id', $region->city_id ?? '') == $cityOption->id)>
                {{ $cityOption->name }} ({{ $cityOption->state->name ?? 'N/A' }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $region->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug (optional)</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $region->slug ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" @selected(old('is_active', $region->is_active ?? 1) == 1)>Active</option>
        <option value="0" @selected(old('is_active', $region->is_active ?? 1) == 0)>Inactive</option>
    </select>
</div>

