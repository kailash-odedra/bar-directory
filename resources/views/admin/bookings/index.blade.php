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
          <h5 class="mb-0">Bookings List</h5>
          <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">Add New Booking</a>
        </div>

        <table id="html5-extension" class="table dt-table-hover" style="width:100%">
          <thead>
            <tr>
              <th>Bar</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td>{{ $b->bar->name ?? '-' }}</td>
              <td>
                <button class="btn btn-sm btn-info view-details-btn" data-id="{{ $b->id }}" data-bs-toggle="modal" data-bs-target="#bookingDetailsModal">
                  <i class="feather icon-eye"></i> View
                </button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        {{ $bookings->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal with Iframe -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookingDetailsModalLabel">Booking Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="bookingDetailsIframe" src="" style="width: 100%; height: 600px; border: none;"></iframe>
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
    const csrf = '{{ csrf_token() }}';

    // Handle View Details button click
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            const iframe = document.getElementById('bookingDetailsIframe');
            iframe.src = `/admin/bookings/${bookingId}`;
        });
    });

    // Reset iframe when modal is closed
    document.getElementById('bookingDetailsModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('bookingDetailsIframe').src = '';
    });

});
</script>
@endsection
