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
                    <h5 class="mb-0">Roles List</h5>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Add New Role</a>
                </div>

                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td><span class="badge bg-primary">{{ $role->users_count }}</span></td>
                            <td><span class="badge bg-info">{{ $role->permissions_count }}</span></td>
                            <td>
                                <button type="button" 
                                    class="btn btn-sm toggle-status-btn {{ $role->is_active == 1 ? 'btn-success' : 'btn-danger' }}" 
                                    data-id="{{ $role->id }}">
                                    {{ $role->is_active == 1 ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $roles->links() }}
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
            const roleId = this.dataset.id;
            const btn = this;
            fetch(`/admin/roles/${roleId}/toggle-status`, {
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

