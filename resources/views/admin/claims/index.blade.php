@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
@vite([
    'resources/scss/light/plugins/table/datatable/dt-global_style.scss',
    'resources/scss/light/plugins/table/datatable/custom_dt_miscellaneous.scss',
    'resources/scss/dark/plugins/table/datatable/dt-global_style.scss',
    'resources/scss/dark/plugins/table/datatable/custom_dt_miscellaneous.scss'
])
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area mt-3">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <h5 class="mb-0">Bar Claims List</h5>
                </div>

                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Bar Name</th>
                            <th>Owner Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($claims as $claim)
                        <tr>
                            <td>{{ $claim->claim_request_id ?? 'N/A' }}</td>
                            <td>{{ $claim->bar->name ?? 'N/A' }}</td>
                            <td>{{ $claim->full_name ?? ($claim->user->name ?? 'N/A') }}</td>
                            <td>{{ $claim->email_address ?? ($claim->user->email ?? 'N/A') }}</td>
                            <td>{{ $claim->phone_number ?? '-' }}</td>
                            <td>{{ $claim->role ?? '-' }}</td>
                            <td>
                                @if($claim->verification_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($claim->verification_status == 'needs_info')
                                    <span class="badge bg-info">Needs Info</span>
                                @elseif($claim->verification_status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($claim->verification_status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($claim->status ?? 'Pending') }}</span>
                                @endif
                            </td>
                            <td>{{ formatDate($claim->created_at) }}</td>
                            <td>
                                <a href="{{ route('admin.claims.show', $claim) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $claims->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('plugins/src/global/vendors.min.js')}}"></script>
@vite(['resources/js/custom.js'])
<script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/custom_miscellaneous.js')}}"></script>
@endsection
