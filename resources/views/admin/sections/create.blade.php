@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($section) ? 'Edit Section' : 'Add New Section' }}</h4>
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

            <form class="row g-3" action="{{ isset($section) ? route('admin.sections.update', $section) : route('admin.sections.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($section)) @method('PUT') @endif
                
                <div class="col-md-6">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $section->title ?? '') }}" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Slug (optional, auto-generated if empty)</label>
                    <input type="text" class="form-control" name="slug" value="{{ old('slug', $section->slug ?? '') }}">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Section Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="section_type" required>
                        <option value="custom" {{ (old('section_type', $section->section_type ?? 'custom') == 'custom') ? 'selected' : '' }}>Custom</option>
                        <option value="hero" {{ (old('section_type', $section->section_type ?? '') == 'hero') ? 'selected' : '' }}>Hero</option>
                        <option value="about" {{ (old('section_type', $section->section_type ?? '') == 'about') ? 'selected' : '' }}>About</option>
                        <option value="services" {{ (old('section_type', $section->section_type ?? '') == 'services') ? 'selected' : '' }}>Services</option>
                        <option value="testimonials" {{ (old('section_type', $section->section_type ?? '') == 'testimonials') ? 'selected' : '' }}>Testimonials</option>
                        <option value="features" {{ (old('section_type', $section->section_type ?? '') == 'features') ? 'selected' : '' }}>Features</option>
                        <option value="gallery" {{ (old('section_type', $section->section_type ?? '') == 'gallery') ? 'selected' : '' }}>Gallery</option>
                        <option value="contact" {{ (old('section_type', $section->section_type ?? '') == 'contact') ? 'selected' : '' }}>Contact</option>
                        <option value="footer" {{ (old('section_type', $section->section_type ?? '') == 'footer') ? 'selected' : '' }}>Footer</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Page (leave empty for global sections)</label>
                    <select class="form-select" name="page">
                        <option value="">Global (All Pages)</option>
                        <option value="home" {{ (old('page', $section->page ?? '') == 'home') ? 'selected' : '' }}>Home</option>
                        <option value="about" {{ (old('page', $section->page ?? '') == 'about') ? 'selected' : '' }}>About</option>
                        <option value="services" {{ (old('page', $section->page ?? '') == 'services') ? 'selected' : '' }}>Services</option>
                        <option value="contact" {{ (old('page', $section->page ?? '') == 'contact') ? 'selected' : '' }}>Contact</option>
                        <option value="bars" {{ (old('page', $section->page ?? '') == 'bars') ? 'selected' : '' }}>Bars</option>
                        <option value="events" {{ (old('page', $section->page ?? '') == 'events') ? 'selected' : '' }}>Events</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Order (for sorting, lower numbers appear first)</label>
                    <input type="number" class="form-control" name="order" value="{{ old('order', $section->order ?? 0) }}" min="0">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                            {{ (old('is_active', $section->is_active ?? true) ? 'checked' : '') }}>
                        <label class="form-check-label">Active (show on frontend)</label>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Content (HTML/WYSIWYG)</label>
                    <textarea class="form-control" name="content" rows="10" id="contentEditor">{{ old('content', $section->content ?? '') }}</textarea>
                    <small class="text-muted">You can use HTML tags or a WYSIWYG editor here</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Section Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    @if(isset($section) && $section->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$section->image) }}" width="150" class="img-thumbnail">
                        <p class="text-muted small mt-1">Current image</p>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description', $section->meta_description ?? '') }}</textarea>
                </div>
                
                <div class="col-md-12">
                    <label class="form-label">Meta Keywords</label>
                    <input type="text" class="form-control" name="meta_keywords" value="{{ old('meta_keywords', $section->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3">
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($section) ? 'Update' : 'Create' }}</button>
                    <a href="{{ route('admin.sections.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Simple content editor enhancement (you can integrate a full WYSIWYG editor like TinyMCE, CKEditor, etc.)
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('contentEditor');
    if (editor) {
        // You can initialize a WYSIWYG editor here
        // Example: tinymce.init({ selector: '#contentEditor' });
    }
});
</script>
@endsection

