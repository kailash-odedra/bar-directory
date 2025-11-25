@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
  <div class="widget-content widget-content-area br-8">
    <div class="statbox widget box box-shadow p-3">

      <div class="widget-header">
        <h4>{{ isset($booking) ? 'Edit Booking' : 'Add New Booking' }}</h4>
      </div>

      @if($errors->any())
      <div class="alert alert-danger mt-3">
        <ul>
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form action="{{ isset($booking) ? route('admin.bookings.update', $booking->id) : route('admin.bookings.store') }}" method="POST">
        @csrf
        @if(isset($booking)) @method('PUT') @endif

        <div class="row">

          <div class="col-md-6 mb-3">
            <label class="form-label">Bar</label>
            <select name="bar_id" class="form-control" required>
              <option value="">Select Bar</option>
              @foreach($bars as $bar)
                <option value="{{ $bar->id }}" {{ old('bar_id', $booking->bar_id ?? '') == $bar->id ? 'selected' : '' }}>
                  {{ $bar->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name', $booking->customer_name ?? '') }}">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Customer Phone</label>
            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $booking->customer_phone ?? '') }}">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Customer Email</label>
            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $booking->customer_email ?? '') }}">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">People Count</label>
            <input type="number" min="1" name="people_count" class="form-control" value="{{ old('people_count', $booking->people_count ?? 1) }}">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Booking Date</label>
            <input type="date" name="booking_date" class="form-control" required value="{{ old('booking_date', isset($booking) && $booking->booking_date ? $booking->booking_date->format('Y-m-d') : '') }}">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Booking Time</label>
            <input type="time" name="booking_time" class="form-control" required value="{{ old('booking_time', isset($booking) ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '') }}">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Duration (minutes)</label>
            <input type="number" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $booking->duration_minutes ?? 120) }}">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Table / Area (optional)</label>
            <input type="text" name="table_area" class="form-control" value="{{ old('table_area', $booking->table_area ?? '') }}">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Price (optional)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $booking->price ?? '') }}">
          </div>

          <div class="col-md-12 mb-3">
            <label class="form-label">Special Request</label>
            <textarea name="special_request" class="form-control" rows="3">{{ old('special_request', $booking->special_request ?? '') }}</textarea>
          </div>

          <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="pending" {{ old('status', $booking->status ?? 'pending') == 'pending' ? 'selected':'' }}>Pending</option>
              <option value="confirmed" {{ old('status', $booking->status ?? '') == 'confirmed' ? 'selected':'' }}>Confirmed</option>
              <option value="cancelled" {{ old('status', $booking->status ?? '') == 'cancelled' ? 'selected':'' }}>Cancelled</option>
              <option value="completed" {{ old('status', $booking->status ?? '') == 'completed' ? 'selected':'' }}>Completed</option>
            </select>
          </div>

        </div>

        <button type="submit" class="btn btn-primary">{{ isset($booking) ? 'Update' : 'Create' }}</button>
      </form>

    </div>
  </div>
</div>
@endsection
