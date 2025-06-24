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
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Bahagian / Unit</th>
                            <td>{{ $datautama->department->name }}</td>
                        </tr>
                        <tr>
                            <th>Tajuk Data</th>
                            <td>{{ $datautama->jenisDataPtj->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Adakah ini KPI?</th>
                            <td>{{ $datautama->is_kpi ? 'Ya' : 'Tidak' }}</td>
                        </tr>
                        <tr>
                            <th>No. PI</th>
                            <td>{{ $datautama->pi_no ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Sasaran PI</th>
                            <td>{{ $datautama->pi_target ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Pautan Dokumen</th>
                            <td>
                                @if (!empty($datautama->doc_link))
                                    <a href="{{ $datautama->doc_link }}" target="_blank" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Buka pautan shared folder">
                                        <i class='bx bxs-folder-open' style="font-size: 1.2rem; color: #007bff;"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah Mengikut Tahun</th>
                            <td>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach ($tahunList as $tahun)
                                                <th>{{ $tahun->tahun }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($tahunList as $tahun)
                                                @php
                                                    $jumlah = $datautama->jumlahs->firstWhere('tahun_id', $tahun->id);
                                                @endphp
                                                <td>{{ $jumlah->jumlah ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-start border-4 border-primary">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 40%">Dicipta oleh</th>
                            <td>{{ $datautama->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tarikh Cipta</th>
                            <td>{{ $datautama->created_at ? $datautama->created_at->format('d/m/Y h:i A') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dikemaskini oleh</th>
                            <td>{{ $datautama->updater->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tarikh Kemaskini</th>
                            <td>{{ $datautama->updated_at ? $datautama->updated_at->format('d/m/Y h:i A') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- End Campus Information Table -->
    <!-- End Page Wrapper -->
@endsection
