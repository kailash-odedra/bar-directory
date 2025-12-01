<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $country->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Slug (optional)</label>
    <input type="text" name="slug" class="form-control" value="{{ old('slug', $country->slug ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">ISO Code</label>
    <input type="text" name="iso_code" class="form-control" value="{{ old('iso_code', $country->iso_code ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-select">
        <option value="1" @selected(old('is_active', $country->is_active ?? 1) == 1)>Active</option>
        <option value="0" @selected(old('is_active', $country->is_active ?? 1) == 0)>Inactive</option>
    </select>
</div>

