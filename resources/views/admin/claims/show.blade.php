@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-sm-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                        <div class="row">
                            <div class="col-12">
                                <h4>Claim Verification - {{ $claim->claim_request_id ?? 'N/A' }}</h4>
                            </div>
                        </div>
                </div>
                <div class="widget-content widget-content-area">
                    <!-- Claim Information -->
                    <div class="mb-4">
                        <h5 class="mb-3">Claim Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Bar Name:</strong><br>
                                @if($claim->bar_id && $claim->bar)
                                    <a href="{{ route('admin.bar.edit', $claim->bar) }}" target="_blank">
                                        {{ $claim->bar->name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Claim Request ID:</strong><br>
                                {{ $claim->claim_request_id ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Status:</strong><br>
                                @if($claim->verification_status == 'pending')
                                    <span class="badge bg-warning">Pending Verification</span>
                                @elseif($claim->verification_status == 'needs_info')
                                    <span class="badge bg-info">Needs More Information</span>
                                @elseif($claim->verification_status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($claim->verification_status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($claim->status ?? 'Pending') }}</span>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Submitted:</strong><br>
                                {{ $claim->created_at ? $claim->created_at->format('F d, Y h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Owner Information -->
                    <div class="mb-4">
                        <h5 class="mb-3">Owner Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Full Name:</strong><br>
                                {{ $claim->full_name ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                @if($claim->email_address)
                                    <a href="mailto:{{ $claim->email_address }}">{{ $claim->email_address }}</a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Phone:</strong><br>
                                @if($claim->phone_number)
                                    <a href="tel:{{ $claim->phone_number }}">{{ $claim->phone_number }}</a>
                                @else
                                    N/A
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Role:</strong><br>
                                {{ $claim->role ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Comments -->
                    @if($claim->comments)
                    <div class="mb-4">
                        <h5 class="mb-3">Owner Comments</h5>
                        <p>{{ $claim->comments }}</p>
                    </div>
                    @endif

                    <!-- Relationship Proof -->
                    @if($claim->relationship_proof)
                    <div class="mb-4">
                        <h5 class="mb-3">Relationship Proof Document</h5>
                        <a href="{{ asset('storage/'.$claim->relationship_proof) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            View Document
                        </a>
                    </div>
                    @endif

                    <!-- Admin Documents -->
                    @if($claim->admin_documents && count($claim->admin_documents) > 0)
                    <div class="mb-4">
                        <h5 class="mb-3">Admin Documents</h5>
                        @foreach($claim->admin_documents as $doc)
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$doc) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                View Document {{ $loop->iteration }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Admin Notes -->
                    @if($claim->admin_notes)
                    <div class="mb-4">
                        <h5 class="mb-3">Admin Notes</h5>
                        <div class="alert alert-info">
                            {{ $claim->admin_notes }}
                        </div>
                    </div>
                    @endif

                    <!-- Verification Info -->
                    @if($claim->verified_at)
                    <div class="mb-4">
                        <h5 class="mb-3">Verification Details</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Verified At:</strong><br>
                                {{ $claim->verified_at ? $claim->verified_at->format('F d, Y h:i A') : 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Verified By:</strong><br>
                                {{ $claim->verifiedBy->name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Admin Actions Panel -->
        <div class="col-xl-4 col-lg-4 col-sm-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <h4>Admin Actions</h4>
                </div>
                <div class="widget-content widget-content-area">
                    <!-- Quick Actions -->
                    @if($claim->verification_status != 'approved' && $claim->verification_status != 'rejected')
                    <div class="mb-3">
                        <button class="btn btn-success w-100 mb-2" onclick="approveClaim({{ $claim->id }})">
                            Approve Claim
                        </button>
                        <button class="btn btn-danger w-100 mb-2" onclick="rejectClaim({{ $claim->id }})">
                            Reject Claim
                        </button>
                    </div>
                    @endif

                    <!-- Request More Information -->
                    <div class="mb-3">
                        <h6>Request More Information</h6>
                        <form id="requestInfoForm">
                            <textarea class="form-control mb-2" name="admin_notes" rows="3" placeholder="Enter message to owner..." required></textarea>
                            <button type="submit" class="btn btn-info w-100">Send Request</button>
                        </form>
                    </div>

                    <!-- Add Internal Notes -->
                    <div class="mb-3">
                        <h6>Add Internal Notes</h6>
                        <form id="addNotesForm">
                            <textarea class="form-control mb-2" name="admin_notes" rows="3" placeholder="Add internal notes..." required>{{ $claim->admin_notes ?? '' }}</textarea>
                            <button type="submit" class="btn btn-secondary w-100">Save Notes</button>
                        </form>
                    </div>

                    <!-- Attach Documents -->
                    <div class="mb-3">
                        <h6>Attach Documents</h6>
                        <form id="attachDocsForm" enctype="multipart/form-data">
                            <input type="file" class="form-control mb-2" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <button type="submit" class="btn btn-warning w-100">Attach Documents</button>
                        </form>
                    </div>

                    <!-- Delete Claim -->
                    <div class="mt-4">
                        <form action="{{ route('admin.claims.destroy', $claim) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this claim?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">Delete Claim</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function approveClaim(id) {
    if (!confirm('Are you sure you want to approve this claim? This will grant the owner edit permissions.')) {
        return;
    }
    
    fetch(`{{ route('admin.claims.approve', $claim) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Claim approved successfully!');
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
    });
}

function rejectClaim(id) {
    if (!confirm('Are you sure you want to reject this claim?')) {
        return;
    }
    
    fetch(`{{ route('admin.claims.reject', $claim) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Claim rejected.');
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
    });
}

document.getElementById('requestInfoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(`{{ route('admin.claims.requestMoreInfo', $claim) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Request sent successfully!');
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
    });
});

document.getElementById('addNotesForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(`{{ route('admin.claims.addNotes', $claim) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Notes saved successfully!');
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
    });
});

document.getElementById('attachDocsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(`{{ route('admin.claims.attachDocuments', $claim) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Documents attached successfully!');
            location.reload();
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endsection

