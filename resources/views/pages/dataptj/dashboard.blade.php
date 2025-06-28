@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        <!-- PAGE TITLE + FILTER INLINE -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h3 class="fw-bold text-primary mb-2 mb-md-0">DATA WAR ROOM DASHBOARD</h3>
            <form id="dashboardFilter" action="{{ route('dataptj.dashboard') }}" method="GET"
                class="d-flex align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <select name="department_id" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                        <option value="">📌 Semua Bahagian</option>
                        @foreach ($departmentList as $department)
                            <option value="{{ $department->id }}"
                                {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <button id="resetButton" type="button" class="btn btn-sm btn-outline-dark shadow-sm">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        @php
            $currentYear = now()->year;
            $colorPalette = [
                '#4e73df', // Formal Blue
                '#6c5ce7', // Professional Purple
                '#8e44ad', // Muted Violet
                '#c0392b', // Classic Red
                '#d35400', // Burnt Orange
                '#f39c12', // Golden Amber
                '#27ae60', // Corporate Green
                '#16a085', // Teal
                '#2980b9', // Business Blue
                '#3498db', // Calmer Blue
                '#95a5a6', // Muted Grey
                '#7f8c8d', // Darker Grey
                '#34495e', // Navy Grey
                '#2c3e50', // Deep Navy
            ];

            $departmentColors = [];
            foreach ($departmentList as $i => $dept) {
                $departmentColors[$dept->id] = $colorPalette[$i] ?? '#6c757d';
            }
        @endphp

        @forelse ($dataList as $departmentName => $dataItems)
            @php
                $deptId = optional($dataItems->first())->department_id ?? null;
                $headerColor = $departmentColors[$deptId] ?? '#6c757d';
                $gradient = "linear-gradient(135deg, $headerColor, #ffffff)";
            @endphp

            <!-- DEPARTMENT SECTION -->
            <div class="mb-5">
                <div class="px-3 py-2 mb-3 rounded-3 shadow-sm" style="background: {{ $gradient }}; color: #fff;">
                    <h4 class="fw-bold m-0 text-uppercase">
                        <i class="bi bi-building"></i> {{ $departmentName }}
                    </h4>
                </div>

                <div class="row g-4">
                    @foreach ($dataItems as $item)
                        @php
                            $jumlahRecord = $item->jumlahs->firstWhere('tahun.tahun', $currentYear);
                            $jumlah = $jumlahRecord->jumlah ?? null;
                            $pi_no = $jumlahRecord->pi_no ?? '-';
                            $pi_target = $jumlahRecord->pi_target ?? null;
                            $jenis = $item->jenis_nilai ?? 'Bilangan';

                            $jumlahPaparan = '-';
                            if (!is_null($jumlah)) {
                                $jumlahPaparan =
                                    $jenis === 'Peratus'
                                        ? $jumlah . ' %'
                                        : ($jenis === 'Mata Wang'
                                            ? 'RM ' . number_format($jumlah, 2)
                                            : $jumlah);
                            }

                            $piTargetPaparan = '-';
                            if (!is_null($pi_target)) {
                                $piTargetPaparan =
                                    $jenis === 'Peratus'
                                        ? $pi_target . ' %'
                                        : ($jenis === 'Mata Wang'
                                            ? 'RM ' . number_format($pi_target, 2)
                                            : $pi_target);
                            }
                        @endphp

                        <!-- CARD DESIGN -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden"
                                style="background-color: {{ $headerColor }}; color: #fff;">
                                <div class="card-body d-flex flex-column p-4">

                                    <!-- NAMA DATA -->
                                    <div class="text-center mb-2">
                                        <div class="text-uppercase fw-semibold"
                                            style="opacity: 0.95; font-size: 0.95rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.4);">
                                            {{ $item->nama_data ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="mx-auto mb-3"
                                        style="width: 50%; height: 1px; background-color: rgba(255,255,255,0.25);"></div>

                                    <!-- NILAI UTAMA -->
                                    <div class="text-center mb-3">
                                        <div class="fw-bold display-5">{{ $jumlahPaparan }}</div>
                                    </div>

                                    <!-- INFO DETAIL TABLE -->
                                    <div class="mb-3">
                                        <table class="table table-sm table-borderless text-white mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>Tahun</td>
                                                    <td class="text-end fw-semibold">{{ $currentYear }}</td>
                                                </tr>
                                                <tr>
                                                    <td>PI No</td>
                                                    <td class="text-end fw-semibold">{{ $pi_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Sasaran</td>
                                                    <td class="text-end fw-semibold">{{ $piTargetPaparan }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Kemaskini</td>
                                                    <td class="text-end fw-semibold">
                                                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') ?? '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- BUTTONS -->
                                    <div class="mt-auto d-flex justify-content-between gap-2">
                                        @if (!empty($item->doc_link))
                                            <a href="{{ $item->doc_link }}" target="_blank"
                                                class="btn btn-sm btn-light rounded-pill"
                                                style="color: {{ $headerColor }};">
                                                <i class="bx bxs-folder-open"></i> Shared Folder
                                            </a>
                                        @endif
                                        <a href="{{ route('dataptj.show', $item->id) }}"
                                            class="btn btn-sm btn-light rounded-pill" style="color: {{ $headerColor }};">
                                            <i class="bx bx-show"></i> Papar Maklumat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="alert alert-warning text-center">Tiada data dijumpai.</div>
        @endforelse
    </div>

    <script>
        document.getElementById('resetButton').addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('department_id');
            window.location.href = url.toString();
        });
    </script>
@endsection
