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
                    <h5 class="mb-0">Events List</h5>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Add New Event</a>
                </div>

                <table id="html5-extension" class="table dt-table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Bar</th>
                            <th>Image</th>
                            <th>Ticket URL</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->bar->name ?? '-' }}</td>
                            <td>
                                @if($event->image)
                                <img src="{{ asset('storage/'.$event->image) }}" width="50">
                                @endif
                            </td>
                            <td><a href="{{ $event->ticket_link }}" target="_blank">Link</a></td>
                            <td>{{ $event->start_time }}</td>
                            <td>{{ $event->end_time }}</td>
                            <td>
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $events->links() }}
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
