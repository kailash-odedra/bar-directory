@extends('layouts.blank')

@section('content')
<div class="booking-container">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-sm-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h4 class="mb-0">Booking Details - #{{ $booking->id }}</h4>
                </div>
                <div class="card-body">
                    <!-- Booking Status Badge -->
                    <div class="mb-4 text-center">
                        @php
                            $statusClass = $booking->status === 'confirmed' ? 'bg-success' : 
                                          ($booking->status === 'cancelled' ? 'bg-danger' : 
                                          ($booking->status === 'completed' ? 'bg-primary' : 'bg-warning'));
                        @endphp
                        <span class="badge {{ $statusClass }} fs-6 px-4 py-2">{{ strtoupper($booking->status) }}</span>
                    </div>

                    <!-- Booking Information -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Bar Information</h6>
                                    <h5 class="mb-1">{{ $booking->bar->name ?? 'N/A' }}</h5>
                                    @if($booking->bar && $booking->bar->location)
                                        <p class="text-muted mb-0">
                                            <i class="feather icon-map-pin"></i> 
                                            {{ $booking->bar->location->address ?? '' }}
                                            @if($booking->bar->location->city)
                                                , {{ $booking->bar->location->city }}
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Booking Reference</h6>
                                    <h5 class="mb-1">#{{ $booking->id }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="feather icon-calendar"></i> 
                                        Created: {{ formatDate($booking->created_at, true) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="mb-4">
                        <h5 class="mb-3">Customer Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <strong>Name:</strong><br>
                                <span class="text-primary">{{ $booking->customer_name }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Email:</strong><br>
                                @if($booking->customer_email)
                                    <a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <strong>Phone:</strong><br>
                                @if($booking->customer_phone)
                                    <a href="tel:{{ $booking->customer_phone }}">{{ $booking->customer_phone }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="mb-4">
                        <h5 class="mb-3">Booking Details</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="info-box">
                                    <strong>Date:</strong><br>
                                    <span class="text-primary">
                                        {{ formatDate($booking->booking_date) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="info-box">
                                    <strong>Time:</strong><br>
                                    <span class="text-primary">
                                        @if($booking->booking_time)
                                            {{ \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="info-box">
                                    <strong>Duration:</strong><br>
                                    <span class="text-primary">
                                        {{ $booking->duration_minutes ?? 120 }} minutes
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="info-box">
                                    <strong>Ends At:</strong><br>
                                    <span class="text-primary">
                                        {{ $booking->ends_at ? $booking->ends_at->format('h:i A') : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">People Count</h6>
                                    <h3 class="mb-0 text-primary">{{ $booking->people_count }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Table / Area</h6>
                                    <h5 class="mb-0">{{ $booking->table_area ?? 'Not Specified' }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Price</h6>
                                    <h5 class="mb-0">
                                        @if($booking->price)
                                            ${{ number_format($booking->price, 2) }}
                                        @else
                                            <span class="text-muted">Not Set</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Request -->
                    @if($booking->special_request)
                    <div class="mb-4">
                        <h5 class="mb-3">Special Request</h5>
                        <div class="alert alert-info">
                            <p class="mb-0">{{ $booking->special_request }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Booking Timeline -->
                    <div class="mb-4">
                        <h5 class="mb-3">Booking Timeline</h5>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <h6>Booking Created</h6>
                                    <p class="text-muted mb-0">
                                        {{ formatDate($booking->created_at, true) }}
                                        @if($booking->created_by_admin)
                                            <span class="badge bg-info ms-2">By Admin</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if($booking->updated_at && $booking->updated_at != $booking->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6>Last Updated</h6>
                                    <p class="text-muted mb-0">{{ formatDate($booking->updated_at, true) }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-primary me-2">
                            <i class="feather icon-edit"></i> Edit Booking
                        </a>
                        @if($booking->status != 'confirmed')
                        <button class="btn btn-success me-2" onclick="changeStatus({{ $booking->id }}, 'confirmed')">
                            <i class="feather icon-check"></i> Confirm
                        </button>
                        @endif
                        @if($booking->status != 'cancelled')
                        <button class="btn btn-danger me-2" onclick="changeStatus({{ $booking->id }}, 'cancelled')">
                            <i class="feather icon-x"></i> Cancel
                        </button>
                        @endif
                        @if($booking->status != 'completed' && $booking->status == 'confirmed')
                        <button class="btn btn-info" onclick="changeStatus({{ $booking->id }}, 'completed')">
                            <i class="feather icon-check-circle"></i> Mark Complete
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-box {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #007bff;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -32px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}
</style>
@endsection

@section('scripts')
<script>
function changeStatus(id, status) {
    if (!confirm(`Are you sure you want to change status to "${status}"?`)) {
        return;
    }
    
    fetch(`/admin/bookings/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully!');
            location.reload();
        } else {
            alert(data.message || 'Unable to change status');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Request failed');
    });
}
</script>
@endsection

