@extends('layouts.app')

@section('content')
<div class="layout-px-spacing">
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12 mx-auto layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-12">
                            <h4>Claim This Bar</h4>
                            @if($bar)
                            <p class="text-muted">Claiming: <strong>{{ $bar->name }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('claims.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if($bar)
                        <input type="hidden" name="bar_id" value="{{ $bar->id }}">
                        @else
                        <div class="mb-3">
                            <label class="form-label">Select Bar <span class="text-danger">*</span></label>
                            <select class="form-select" name="bar_id" required>
                                <option value="">-- Select Bar --</option>
                                @foreach(\App\Models\Bar::orderBy('name')->get() as $barOption)
                                <option value="{{ $barOption->id }}" {{ old('bar_id') == $barOption->id ? 'selected' : '' }}>
                                    {{ $barOption->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email_address" value="{{ old('email_address') }}" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="role" required>
                                    <option value="">-- Select Role --</option>
                                    <option value="Owner" {{ old('role') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                    <option value="Manager" {{ old('role') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="Marketing Lead" {{ old('role') == 'Marketing Lead' ? 'selected' : '' }}>Marketing Lead</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Relationship Proof Document (Optional)</label>
                            <input type="file" class="form-control" name="relationship_proof" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Upload documents like utility bill, business license, tax certificate, lease agreement, etc.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comments / Additional Information (Optional)</label>
                            <textarea class="form-control" name="comments" rows="4" placeholder="Tell us about your relationship with this bar...">{{ old('comments') }}</textarea>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> After submitting this form, our admin team will review your claim. 
                            You may be contacted for additional verification if needed.
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Submit Claim Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

