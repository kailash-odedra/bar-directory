@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="widget-content widget-content-area br-8">
        <div class="statbox widget box box-shadow p-3">
            <div class="widget-header">
                <h4>{{ isset($event) ? 'Edit Event' : 'Add New Event' }}</h4>
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

            <form class="row g-3" action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($event)) @method('PUT') @endif
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type" required>
                        <option value="event" {{ (old('type', $event->type ?? '') == 'event') ? 'selected' : '' }}>Event</option>
                        <option value="offer" {{ (old('type', $event->type ?? '') == 'offer') ? 'selected' : '' }}>Offer</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $event->title ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bar</label>
                    <select class="form-select" name="bar_id" required>
                        <option value="">Select Bar</option>
                        @foreach($bars as $bar)
                        <option value="{{ $bar->id }}" {{ (isset($event) && $event->bar_id==$bar->id) ? 'selected' : '' }}>
                            {{ $bar->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ticket URL</label>
                    <input type="url" class="form-control" name="ticket_link" value="{{ old('ticket_link', $event->ticket_link ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Start Date & Time</label>
                    <input type="datetime-local" class="form-control" name="start_time" value="{{ isset($event) ? date('Y-m-d\TH:i', strtotime($event->start_time)) : '' }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date & Time</label>
                    <input type="datetime-local" class="form-control" name="end_time" value="{{ isset($event) ? date('Y-m-d\TH:i', strtotime($event->end_time)) : '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image</label>
                    <input type="file" class="form-control" name="image">
                    @if(isset($event) && $event->image)
                    <img src="{{ asset('storage/'.$event->image) }}" width="100" class="mt-2">
                    @endif
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description">{{ old('description', $event->description ?? '') }}</textarea>
                </div>
                
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">{{ isset($event) ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
