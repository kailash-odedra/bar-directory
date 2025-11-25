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
                    <h5 class="mb-0">Claim List</h5>
                </div>
                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Bar</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Claimed</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($claims as $claim)
                        <tr>
                            <td>{{ $claim->bar->name }}</td>
                            <td>{{ $claim->user->name }}</td>
                            <td>{{ ucfirst($claim->status) }}</td>
                            <td>
                                <button class="btn btn-success btn-sm approve-btn" data-id="{{ $claim->id }}">Approve</button>
                                <button class="btn btn-danger btn-sm reject-btn" data-id="{{ $claim->id }}">Reject</button>
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
<script src="{{asset('plugins/src/table/datatable/button-ext/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/jszip.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/buttons.html5.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/button-ext/buttons.print.min.js')}}"></script>
<script src="{{asset('plugins/src/table/datatable/custom_miscellaneous.js')}}"></script>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('approve-btn')) {
            let id = e.target.dataset.id;
            fetch(`/admin/claims/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    location.reload();
                }
            });
        }
        if (e.target.classList.contains('reject-btn')) {
            let id = e.target.dataset.id;
            fetch(`/admin/claims/${id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    location.reload();
                }
            });
        }

    });
});

</script>