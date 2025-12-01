<div class="mb-3">
    <label class="form-label">Country</label>
    <select name="country_id" class="form-select" required>
        <option value="">Select country</option>
        @foreach($countries as $country)
            <option value="{{ $country->id }}" @selected(old('country_id', $state->country_id ?? '') == $country->id)>
                {{ $country->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $state->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug (optional)</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $state->slug ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" @selected(old('is_active', $state->is_active ?? 1) == 1)>Active</option>
        <option value="0" @selected(old('is_active', $state->is_active ?? 1) == 0)>Inactive</option>
    </select>
</div>

