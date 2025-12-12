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
                    <h5 class="mb-0">Tags List</h5>
                    <a href="{{ route('admin.bar-tags.create') }}" class="btn btn-primary">Add New Tag</a>
                </div>
                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                            <tr>
                                <td>{{ $tag->name }}</td>
                                <td>{{ $tag->slug }}</td>
                                <td>{{ formatDate($tag->created_at) }}</td>
                                <td>
                                    <button type="button" 
                                        class="btn btn-sm toggle-status-btn {{ $tag->status == 1 ? 'btn-success' : 'btn-danger' }}" 
                                        data-id="{{ $tag->id }}">
                                        {{ $tag->status == 1 ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bar-tags.edit', $tag) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="{{ route('admin.bar-tags.destroy', $tag) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this tag?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $tags->links() }}
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.toggle-status-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const barId = this.dataset.id;
            const btn = this;
            fetch(`/admin/bar-tags/${barId}/toggle-status`, {
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
});
</script>