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

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Bookings</h5>
          <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-sm">+ Add Booking</a>
        </div>

        <!-- <form method="GET" class="row g-2 mb-3">
          <div class="col-auto">
            <input type="text" name="q" class="form-control" placeholder="Search name / phone / email" value="{{ request('q') }}">
          </div>

          <div class="col-auto">
            <select name="bar_id" class="form-control">
              <option value="">All Bars</option>
              @foreach($bars as $b)
                <option value="{{ $b->id }}" {{ request('bar_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-auto">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
          </div>

          <div class="col-auto">
            <select name="status" class="form-control">
              <option value="">All Status</option>
              <option value="pending" {{ request('status')=='pending' ? 'selected':'' }}>Pending</option>
              <option value="confirmed" {{ request('status')=='confirmed' ? 'selected':'' }}>Confirmed</option>
              <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
              <option value="completed" {{ request('status')=='completed' ? 'selected':'' }}>Completed</option>
            </select>
          </div>

          <div class="col-auto">
            <button class="btn btn-secondary">Filter</button>
          </div>
        </form> -->

        <table id="html5-extension" class="table dt-table-hover mt-3" style="width:100%">
          <thead>
            <tr>
              <th>Bar</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>People</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
              <th style="width:220px;">Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach($bookings as $b)
            <tr>
              <td>{{ $b->bar->name ?? '-' }}</td>

              <td>
                {{ $b->customer_name }}
                @if($b->customer_email)
                  <br><small>{{ $b->customer_email }}</small>
                @endif
              </td>

              <td>{{ $b->customer_phone ?? '-' }}</td>

              <td>{{ $b->people_count }}</td>

              <td>{{ optional($b->booking_date)->format('Y-m-d') ?? $b->booking_date }}</td>

              <td>{{ \Carbon\Carbon::parse($b->booking_time)->format('H:i') ?? $b->booking_time }}</td>

              <td>
                @php
                  $cls = $b->status === 'confirmed' ? 'bg-success' : ($b->status === 'cancelled' ? 'bg-danger' : ($b->status === 'completed' ? 'bg-primary' : 'bg-secondary'));
                @endphp
                <span class="badge {{ $cls }}">{{ ucfirst($b->status) }}</span>
              </td>

              <td>
                <a href="{{ route('admin.bookings.edit', $b->id) }}" class="btn btn-info btn-sm">Edit</a>

                <button class="btn btn-success btn-sm change-status-btn" data-id="{{ $b->id }}" data-status="confirmed">Confirm</button>
                <button class="btn btn-warning btn-sm change-status-btn" data-id="{{ $b->id }}" data-status="cancelled">Cancel</button>

                <form action="{{ route('admin.bookings.destroy', $b->id) }}" method="POST" style="display:inline-block;">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-sm" onclick="return confirm('Delete booking?')">Delete</button>
                </form>
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

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('change-status-btn')) {
            const id = e.target.dataset.id;
            const status = e.target.dataset.status;

            if (!confirm('Change status to "'+status+'"?')) return;

            fetch(`/admin/bookings/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Unable to change status');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Request failed');
            });
        }
    });
});
</script>
@endsection
