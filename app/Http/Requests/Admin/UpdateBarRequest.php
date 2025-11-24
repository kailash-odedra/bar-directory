<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('update', $this->route('bar'));
    }

    public function rules()
    {
        $barId = $this->route('bar') ? $this->route('bar')->id : null;
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bars,slug,' . $barId,
            'short_description' => 'nullable|string|max:255',
            'full_description' => 'nullable|string',
            'website' => 'nullable|url',
            'facebook' => 'nullable|url',
            'instagram' => 'nullable|url',
            'video_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:5120',
            'cover_image' => 'nullable|image|max:10240',
            'gallery.*' => 'nullable|image|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            // location
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'zip' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            // timings
            'timing' => 'nullable|array',
            'timing.*.open' => 'nullable|date_format:H:i',
            'timing.*.close' => 'nullable|date_format:H:i',
            'timing.*.closed' => 'nullable|boolean',
        ];
    }
}
