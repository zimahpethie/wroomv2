@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Tahun</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tahun') }}">Senarai Tahun</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Maklumat {{ $tahun->tahun }}</li>
                </ol>
            </nav>
        </div>
        @role('Superadmin')
        <div class="ms-auto">
            <a href="{{ route('tahun.edit', $tahun->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
        @endrole
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">Maklumat {{ $tahun->tahun }}</h6>
    <hr />

    <!-- tahun Information Table -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Tahun</th>
                            <td>{{ $tahun->tahun }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ $tahun->publish_status }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End tahun Information Table -->
    <!-- End Page Wrapper -->
@endsection
