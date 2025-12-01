@extends('layouts.app')

@section('styles')
{{-- Style Here --}}
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" href="{{asset('plugins/src/table/datatable/datatables.css')}}">
    @vite(['resources/scss/light/plugins/table/datatable/dt-global_style.scss'])
    @vite(['resources/scss/light/plugins/table/datatable/custom_dt_miscellaneous.scss'])
    @vite(['resources/scss/dark/plugins/table/datatable/dt-global_style.scss'])
    @vite(['resources/scss/dark/plugins/table/datatable/custom_dt_miscellaneous.scss'])
    <!--  END CUSTOM STYLE FILE  -->
@endsection

@section('content')

<div class="row">

    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-content widget-content-area mt-3">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <h5 class="mb-0">Bars List</h5>
                    <a href="{{ route('admin.bar.create') }}" class="btn btn-primary">Add New Bar</a>
                </div>
                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Owner</th>
                            <th>Tags</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Claimed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bars as $bar)
                            <tr>
                                <td>{{ $bar->name }}</td>
                                <td>{{ $bar->owner ? $bar->owner->name : '-' }}</td>
                                <td>
                                    @foreach($bar->tags as $tag)
                                        <span class="badge bg-info">{{ $tag->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $bar->reviews->avg('rating') ?? 0 }}</td>
                                <td>
                                    <button type="button" 
                                        class="btn btn-sm toggle-status-btn {{ $bar->status == 1 ? 'btn-success' : 'btn-danger' }}" 
                                        data-id="{{ $bar->getRouteKey() }}">
                                        {{ $bar->status == 1 ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    <button type="button" 
                                        class="btn btn-sm toggle-featured-btn {{ $bar->is_featured ? 'btn-warning' : 'btn-secondary' }}" 
                                        data-id="{{ $bar->getRouteKey() }}">
                                        {{ $bar->is_featured ? '⭐ Featured' : 'Not Featured' }}
                                    </button>
                                </td>
                                <td>{{ $bar->claimed ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{ route('admin.bar.edit', $bar) }}" class="btn btn-sm btn-primary">Edit</a>

                                    <form action="{{ route('admin.bar.destroy', $bar) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this bar?')">Delete</button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
{{-- Scripts Here --}}
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
    // Toggle Status
    const statusButtons = document.querySelectorAll('.toggle-status-btn');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const barId = this.dataset.id;
            const btn = this;
            fetch(`/admin/bar/${barId}/toggle-status`, {
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
                if(data.success){
                    if(data.status == 1){
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-success');
                        btn.textContent = 'Active';
                    } else {
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-danger');
                        btn.textContent = 'Inactive';
                    }
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });

    // Toggle Featured
    const featuredButtons = document.querySelectorAll('.toggle-featured-btn');
    featuredButtons.forEach(button => {
        button.addEventListener('click', function() {
            const barId = this.dataset.id;
            const btn = this;
            fetch(`/admin/bar/${barId}/toggle-featured`, {
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
                if(data.success){
                    if(data.is_featured == 1){
                        btn.classList.remove('btn-secondary');
                        btn.classList.add('btn-warning');
                        btn.textContent = '⭐ Featured';
                    } else {
                        btn.classList.remove('btn-warning');
                        btn.classList.add('btn-secondary');
                        btn.textContent = 'Not Featured';
                    }
                }
            })
            .catch(err => console.error('Error:', err));
        });
    });
});
</script>
