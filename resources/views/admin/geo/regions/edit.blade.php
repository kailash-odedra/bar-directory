@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-12 mx-auto layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <h4>Edit Region</h4>
            </div>
            <div class="widget-content widget-content-area">
                <form action="{{ route('admin.regions.update', $region) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.geo.regions.form', ['region' => $region])
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.regions.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button class="btn btn-primary">Update Region</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

