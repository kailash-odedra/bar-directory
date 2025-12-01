@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-8 col-lg-8 col-md-12 mx-auto layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-12">
                        <h4>Edit Country</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form action="{{ route('admin.countries.update', $country) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('admin.geo.countries.form', ['country' => $country])
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.countries.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button class="btn btn-primary">Update Country</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

