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
                    <h5 class="mb-0">Pending Bar Approvals</h5>
                    <div>
                        <button class="btn btn-success" id="bulkApproveBtn" disabled>
                            Approve Selected
                        </button>
                        <a href="{{ route('admin.bar.index') }}" class="btn btn-secondary">All Bars</a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <table id="html5-extension" class="table dt-table-hover mt-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Tags</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bars as $bar)
                            <tr>
                                <td>
                                    <input type="checkbox" class="bar-checkbox" value="{{ $bar->id }}" data-route-key="{{ $bar->getRouteKey() }}">
                                </td>
                                <td>{{ $bar->name }}</td>
                                <td>
                                    @if($bar->location)
                                        {{ $bar->location->city ?? '' }}, {{ $bar->location->state->name ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @foreach($bar->tags as $tag)
                                        <span class="badge bg-info">{{ $tag->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $bar->created_at ? $bar->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.bar.edit', $bar) }}" class="btn btn-sm btn-info">Review</a>
                                    <button type="button" class="btn btn-sm btn-success approve-btn" data-route-key="{{ $bar->getRouteKey() }}">
                                        Approve
                                    </button>
                                    <form action="{{ route('admin.bar.destroy', $bar) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this bar?')">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $bars->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('plugins/src/global/vendors.min.js')}}"></script>
@vite(['resources/js/custom.js'])
<script src="{{asset('plugins/src/table/datatable/datatables.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/jszip.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/buttons.print.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/custom_miscellaneous.js')}}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.bar-checkbox');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');

    // Select All
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkButton();
    });

    // Individual checkbox
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateBulkButton();
            selectAll.checked = Array.from(checkboxes).every(c => c.checked);
        });
    });

    function updateBulkButton() {
        const selected = Array.from(checkboxes).filter(c => c.checked);
        bulkApproveBtn.disabled = selected.length === 0;
        bulkApproveBtn.textContent = selected.length > 0 
            ? `Approve Selected (${selected.length})` 
            : 'Approve Selected';
    }

    // Bulk Approve
    bulkApproveBtn.addEventListener('click', function() {
        const selected = Array.from(checkboxes).filter(c => c.checked);
        const barIds = selected.map(c => c.value);

        if (barIds.length === 0) return;

        if (!confirm(`Approve ${barIds.length} bar(s)?`)) return;

        fetch('{{ route("admin.bar.bulkApprove") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ bar_ids: barIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(err => console.error('Error:', err));
    });

    // Individual Approve
    document.querySelectorAll('.approve-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const routeKey = this.dataset.routeKey;
            
            fetch(`/admin/bar/${routeKey}/approve`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    location.reload();
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });
});
</script>
@endsection

