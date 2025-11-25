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
                
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menu Items</h5>

                    <a href="{{ route('admin.bar-menu-items.create') }}" class="btn btn-primary btn-sm">
                        + Add Menu Item
                    </a>
                </div>

                <table id="html5-extension" class="table dt-table-hover mt-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Bar</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th style="width:150px;">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" 
                                         style="width:60px;height:45px;object-fit:cover;border-radius:5px;">
                                @else
                                    <span class="badge bg-secondary">No Image</span>
                                @endif
                            </td>

                            <td>{{ $item->name }}</td>

                            <td>{{ $item->category->name ?? '-' }}</td>

                            <td>{{ $item->category->bar->name ?? '-' }}</td>

                            <td>
                                ₹{{ number_format($item->price, 2) }}
                            </td>

                           <td>
                                <button type="button" 
                                    class="btn btn-sm toggle-status-btn {{ $item->status == 1 ? 'btn-success' : 'btn-danger' }}" 
                                    data-id="{{ $item->id }}">
                                    {{ $item->status == 1 ? 'Active' : 'Inactive' }}
                                </button>
                            </td>

                            <td>
                                <a href="{{ route('admin.bar-menu-items.edit', $item->id) }}" 
                                   class="btn btn-info btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.bar-menu-items.destroy', $item->id) }}" 
                                      method="POST" 
                                      style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Delete item?')">
                                        Delete
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $items->links() }}

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
    const buttons = document.querySelectorAll('.toggle-status-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const barId = this.dataset.id;
            const btn = this;
            fetch(`/admin/bar-menu-items/${barId}/toggle-status`, {
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
@endsection
