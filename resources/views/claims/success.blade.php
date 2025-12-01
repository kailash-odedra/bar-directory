@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12 mx-auto layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area text-center">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle text-success">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    
                    <h3 class="mb-3">Claim Request Submitted Successfully!</h3>
                    
                    <div class="alert alert-success">
                        <strong>Claim Request ID:</strong> {{ $claim->claim_request_id }}<br>
                        <strong>Bar:</strong> {{ $claim->bar->name ?? 'N/A' }}<br>
                        <strong>Status:</strong> Pending Verification
                    </div>

                    <p class="mb-4">
                        Thank you for submitting your claim request. Our admin team will review your submission 
                        and contact you at <strong>{{ $claim->email_address }}</strong> or <strong>{{ $claim->phone_number }}</strong> 
                        if additional information is needed.
                    </p>

                    <div class="alert alert-info">
                        <h6>What happens next?</h6>
                        <ul class="text-start">
                            <li>Our admin team will review your claim request</li>
                            <li>You may be contacted for additional verification</li>
                            <li>Once approved, you'll receive edit permissions for the bar</li>
                            <li>You'll be notified via email about the status of your claim</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

