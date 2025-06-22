@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Data JKEN</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('datautama') }}">Senarai Data JKEN</a></li>
                <li class="breadcrumb-item active" aria-current="page">Maklumat {{ ucfirst($datautama->name) }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        <a href="{{ route('datautama.edit', $datautama->id) }}">
            <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
        </a>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">Maklumat {{ ucfirst($datautama->name) }}</h6>
<hr />

<!-- Campus Information Table -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Bahagian / Unit</th>
                        <td>{{ $datautama->department->name }}</td>
                    </tr>
                    <tr>
                        <th>Nama Data</th>
                        <td>{{ ucfirst($datautama->name) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $datautama->publish_status }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Campus Information Table -->
<!-- End Page Wrapper -->
@endsection