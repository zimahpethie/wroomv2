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
                                <option value="{{ $department->id }}"
                                    {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
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
    @php
        // Warna unik untuk 14+ jabatan (boleh tambah lagi kalau perlu)
        $colorPalette = [
            'primary',
            'success',
            'warning',
            'danger',
            'info',
            'secondary',
            'dark',
            'indigo',
            'teal',
            'orange',
            'pink',
            'cyan',
            'blue',
            'red',
        ];

        // Padankan department_id kepada warna unik
        $departmentColors = [];
        $i = 0;
        foreach ($departmentList as $dept) {
            $departmentColors[$dept->id] = $colorPalette[$i] ?? 'dark'; // fallback if warna tak cukup
            $i++;
        }
    @endphp

    <div class="row">
        @forelse ($dataList as $departmentName => $dataItems)
            <div class="col-12">
                <h5 class="fw-bold text-dark mt-4">{{ $departmentName }}</h5>
            </div>

            @foreach ($dataItems as $item)
                @php
                    $currentYear = now()->year;
                    $jumlahSemasa = $item->jumlahs->firstWhere('tahun.tahun', $currentYear)->jumlah ?? '-';
                    $color = $departmentColors[$item->department_id] ?? 'dark';
                @endphp

                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm h-100 border-0 rounded-3 bg-{{ $color }} text-white">
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Tajuk -->
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="fw-semibold text-uppercase" style="font-size: 0.9rem;">
                                    {{ $item->nama_data ?? '-' }}
                                </span>
                                <span class="badge bg-light text-{{ $color }}">
                                    {{ $currentYear }}
                                </span>
                            </div>

                            @php
                                $currentYear = now()->year;
                                $jumlahSemasaRecord = $item->jumlahs->firstWhere('tahun.tahun', $currentYear);
                                $jumlahSemasa = $jumlahSemasaRecord->jumlah ?? '-';
                                $jenis = $item->jenis_nilai ?? 'Bilangan';

                                // Format jumlah
                                if (!is_null($jumlahSemasa) && $jumlahSemasa !== '-') {
                                    if ($jenis === 'Peratus') {
                                        $jumlahPaparan = $jumlahSemasa . ' %';
                                    } elseif ($jenis === 'Mata Wang') {
                                        $jumlahPaparan = 'RM ' . number_format($jumlahSemasa, 2);
                                    } else {
                                        $jumlahPaparan = $jumlahSemasa;
                                    }
                                } else {
                                    $jumlahPaparan = '-';
                                }

                                // Format sasaran
                                if (!is_null($jumlahSemasaRecord->pi_target ?? null)) {
                                    if ($jenis === 'Peratus') {
                                        $sasaranPaparan = $jumlahSemasaRecord->pi_target . ' %';
                                    } elseif ($jenis === 'Mata Wang') {
                                        $sasaranPaparan = 'RM ' . number_format($jumlahSemasaRecord->pi_target, 2);
                                    } else {
                                        $sasaranPaparan = $jumlahSemasaRecord->pi_target;
                                    }
                                } else {
                                    $sasaranPaparan = '-';
                                }
                            @endphp


                            <!-- Nilai -->
                            <div class="text-center mb-3">
                                <h1 class="fw-bold display-5 text-white">{{ $jumlahPaparan }}</h1>
                            </div>

                            <!-- Info Ringkas -->
                            <div class="mb-3" style="font-size: 0.9rem;">
                                <div><i class="bi bi-hash"></i> <strong>PI No:</strong> {{ $item->pi_no ?? '-' }}</div>
                                <div><i class="bi bi-bullseye"></i> <strong>Sasaran:</strong> {{ $sasaranPaparan }}</div>
                                <div><i class="bi bi-clock"></i> <strong>Kemaskini:</strong>
                                    {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') }}
                                </div>
                            </div>

                            <!-- Butang: Shared Folder + Paparan Lanjut -->
                            <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
                                @if (!empty($item->doc_link))
                                    <a href="{{ $item->doc_link }}" target="_blank"
                                        class="btn btn-sm btn-light text-{{ $color }} rounded-pill" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Buka pautan shared folder">
                                        <i class="bx bxs-folder-open"></i> Shared Folder
                                    </a>
                                @endif
                                <a href="{{ route('dataptj.show', $item->id) }}"
                                    class="btn btn-sm btn-light text-{{ $color }} rounded-pill">
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
        document.getElementById('resetButton').addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('department_id');
            window.location.href = url.toString();
        });
    </script>
@endsection
