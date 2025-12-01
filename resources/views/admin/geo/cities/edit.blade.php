@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-12 mx-auto layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <h4>Edit City</h4>
            </div>
            <div class="widget-content widget-content-area">
                <form action="{{ route('admin.cities.update', $city) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.geo.cities.form', ['city' => $city])
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.cities.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button class="btn btn-primary">Update City</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

