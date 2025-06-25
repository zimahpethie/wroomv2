@extends('layouts.master')

@section('content')
    <!-- FILTER -->
    <div class="container-fluid mb-4">
        <form id="dashboardFilter" action="{{ route('dataptj.dashboard') }}" method="GET">
            <div class="row justify-content-end align-items-center">
                <div class="col-md-4 col-12 mb-2">
                    <div class="input-group">
                        <select name="department_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Jabatan --</option>
                            @foreach ($departmentList as $department)
                                <option value="{{ $department->id }}" {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <button id="resetButton" class="btn btn-primary" type="button">Reset</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- TAJUK -->
    <div class="text-center mb-4">
        <h4 class="fw-bold text-primary mb-1">DATA WAR ROOM</h4>
        <hr class="w-50 mx-auto border-primary" style="opacity: 0.75">
    </div>

    <!-- KAD -->
    <div class="row">
        @forelse ($dataList as $departmentName => $dataItems)
            <div class="col-12">
                <h5 class="fw-bold text-dark mt-4">{{ $departmentName }}</h5>
            </div>

            @foreach ($dataItems as $item)
                @php
                    $currentYear = now()->year;
                    $jumlahSemasa = $item->jumlahs->firstWhere('tahun.tahun', $currentYear)->jumlah ?? '-';
                @endphp

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm h-100 border-0 rounded-3">
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Tajuk -->
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-uppercase text-primary" style="font-size: 0.9rem;">
                                    {{ $item->nama_data ?? '-' }}
                                </span>
                                <span class="badge bg-primary text-white">
                                    {{ $currentYear }}
                                </span>
                            </div>

                            <!-- Nilai -->
                            <div class="text-center mb-3">
                                <h1 class="fw-bold text-primary display-5">{{ $jumlahSemasa }}</h1>
                            </div>

                            <!-- Info Ringkas -->
                            <div class="mb-3" style="font-size: 0.9rem; color: #111;">
                                <div><i class="bi bi-hash"></i> <strong>PI No:</strong> {{ $item->pi_no ?? '-' }}</div>
                                <div><i class="bi bi-bullseye"></i> <strong>PI Target:</strong> {{ $item->pi_target ?? '-' }}</div>
                                <div><i class="bi bi-clock"></i> <strong>Kemaskini:</strong> 
                                    {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}
                                </div>
                            </div>

                            <!-- Butang -->
                            <div class="mt-auto text-end">
                                <a href="{{ route('dataptj.show', $item->id) }}" class="btn btn-sm btn-primary rounded-pill">
                                    <i class="bx bx-show"></i> Paparan Lanjut
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">Tiada data dijumpai.</div>
            </div>
        @endforelse
    </div>

    <!-- SCRIPT RESET -->
    <script>
        document.getElementById('resetButton').addEventListener('click', function () {
            const url = new URL(window.location.href);
            url.searchParams.delete('department_id');
            window.location.href = url.toString();
        });
    </script>
@endsection
