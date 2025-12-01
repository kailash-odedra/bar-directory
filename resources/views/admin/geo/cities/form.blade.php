<div class="mb-3">
    <label class="form-label">State</label>
    <select name="state_id" class="form-select" required>
        <option value="">Select state</option>
        @foreach($states as $state)
            <option value="{{ $state->id }}" @selected(old('state_id', $city->state_id ?? '') == $state->id)>
                {{ $state->name }} ({{ $state->country->name ?? 'N/A' }})
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $city->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug (optional)</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $city->slug ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" @selected(old('is_active', $city->is_active ?? 1) == 1)>Active</option>
        <option value="0" @selected(old('is_active', $city->is_active ?? 1) == 0)>Inactive</option>
    </select>
</div>

