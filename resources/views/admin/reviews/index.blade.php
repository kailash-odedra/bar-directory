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
                    <h5 class="mb-0">Bar Reviews</h5>
                </div>
                <table id="html5-extension" class="table dt-table-hover mt-3" style="width:100%">
                    <thead>
                        <tr>
                            <th>Bar</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Comment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>{{ $review->bar->name ?? '-' }}</td>
                            <td>{{ $review->user->name ?? '-' }}</td>
                            <td>
                                @for($i=1; $i<=5; $i++)
                                    @if($i <= $review->rating)
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </td>
                            <td style="max-width:250px;">
                                {{ Str::limit($review->comment, 120) }}
                            </td>
                            <td>
                                @if($review->status=='approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($review->status=='hidden')
                                    <span class="badge bg-warning">Hidden</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-success btn-sm approve-btn" data-id="{{ $review->id }}">
                                    Approve
                                </button>
                                <button class="btn btn-warning btn-sm hide-btn" data-id="{{ $review->id }}">
                                    Hide
                                </button>
                                <form action="{{ route('admin.bar-reviews.destroy', $review->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $reviews->links() }}
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
document.addEventListener('DOMContentLoaded', function(){

    document.addEventListener('click', function(e){
        if(e.target.classList.contains('approve-btn')){
            let id = e.target.dataset.id;

            fetch(`/admin/bar-reviews/${id}/approve`, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    location.reload();
                }
            });
        }

        // HIDE REVIEW
        if(e.target.classList.contains('hide-btn')){
            let id = e.target.dataset.id;

            fetch(`/admin/bar-reviews/${id}/hide`, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
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
@endsection
