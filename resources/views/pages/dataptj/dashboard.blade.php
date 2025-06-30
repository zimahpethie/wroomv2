@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        <!-- PAGE TITLE + FILTER INLINE -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h2 class="fw-bold text-primary mb-3 mb-md-0" style="font-size: 1.8rem;">
                DATA WAR ROOM DASHBOARD
            </h2>
            <form id="dashboardFilter" action="{{ route('dataptj.dashboard') }}" method="GET"
                class="d-flex flex-row flex-wrap align-items-center gap-2">
                <div>
                    <select name="department_id" class="form-select form-select-sm rounded-pill shadow-sm"
                        onchange="this.form.submit()">
                        <option value="">📌 Semua Bahagian</option>
                        @foreach ($departmentList as $department)
                            <option value="{{ $department->id }}"
                                {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button id="resetButton" type="button" class="btn btn-sm btn-outline-secondary rounded-pill">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        @php
            $currentYear = now()->year;
            $colorPalette = [
                '#1565C0', // Vivid Blue
                '#2E7D32', // Strong Green
                '#C62828', // Bold Red
                '#EF6C00', // Bold Orange
                '#F9A825', // Strong Yellow
                '#AD1457', // Bold Pink
                '#6A1B9A', // Royal Purple
                '#00897B', // Teal
                '#00ACC1', // Cyan / Turquoise
                '#6D4C41', // Brown
                '#283593', // Navy Blue
                '#9E9D24', // Olive/Lime
                '#424242', // Dark Gray
                '#37474F', // Slate / Blue-Gray
            ];

            $departmentColors = [];
            foreach ($departmentList as $i => $dept) {
                $departmentColors[$dept->id] = $colorPalette[$i] ?? '#6c757d';
            }
        @endphp


        @forelse ($dataList as $departmentName => $dataItems)
            @php
                $deptId = optional($dataItems->first())->department_id ?? null;
                $accentColor = $departmentColors[$deptId] ?? '#6c757d';
                $lightAccent = $accentColor . '33';
            @endphp

            <div class="mb-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-building" style="color: {{ $accentColor }};"></i>
                    <span class="badge rounded-pill shadow-sm text-uppercase fw-bold"
                        style="background-color: {{ $accentColor }}; font-size: 0.9rem;">
                        <i class="bi bi-building-fill me-1"></i> {{ $departmentName }}
                    </span>
                    <div class="flex-fill ms-2" style="height: 1px; background-color: {{ $accentColor }}33;"></div>
                </div>

                <div class="row g-4">
                    @foreach ($dataItems as $item)
                        @php
                            $jumlahRecord = $item->jumlahs->firstWhere('tahun.tahun', $currentYear);
                            $jumlah = $jumlahRecord->jumlah ?? null;
                            $pi_no = $jumlahRecord->pi_no ?? '-';
                            $pi_target = $jumlahRecord->pi_target ?? null;
                            $jenis = $item->jenis_nilai ?? 'Bilangan';

                            // Determine how to format jumlah
                            $jumlahPaparan = '-';
                            if (!is_null($jumlah)) {
                                if ($jenis === 'Peratus') {
                                    $jumlahPaparan = $jumlah . ' %';
                                } elseif ($jenis === 'Mata Wang') {
                                    $jumlahPaparan = 'RM ' . number_format($jumlah, 2);
                                } else {
                                    $jumlahPaparan = number_format($jumlah, 0);
                                }
                            }

                            // Determine how to format pi_target
                            $piTargetPaparan = '-';
                            if (!is_null($pi_target)) {
                                if ($jenis === 'Peratus') {
                                    $piTargetPaparan = $pi_target . ' %';
                                } elseif ($jenis === 'Mata Wang') {
                                    $piTargetPaparan = 'RM ' . number_format($pi_target, 2);
                                } else {
                                    $piTargetPaparan = number_format($pi_target, 0);
                                }
                            }
                        @endphp

                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-98 position-relative">

                                <!-- ACCENT STRIP -->
                                <div
                                    style="height: 4px; background-color: {{ $accentColor }}; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                                </div>

                                <!-- COLORED BORDER LEFT -->
                                <div class="h-98"
                                    style="border-left: 4px solid {{ $accentColor }}; background-color: #fff; border-bottom-right-radius: 0.75rem; border-bottom-left-radius: 0.75rem;">

                                    <!-- COMPACT HEADER -->
                                    <div class="d-flex align-items-center justify-content-center text-center px-2"
                                        style="min-height: 50px; max-height: 60px; background-color: {{ $accentColor }}33; white-space: normal;">
                                        <h6 class="fw-bold text-uppercase mb-0"
                                            style="line-height: 1.2; font-size: 0.85rem; color: {{ $accentColor }};">
                                            {{ $item->nama_data ?? '-' }}
                                        </h6>
                                    </div>

                                    <div class="card-body d-flex flex-column p-3">

                                        <!-- VALUE -->
                                        <div class="text-center mb-1">
                                            <div class="fw-bold" style="font-size: 1.5rem; color: {{ $accentColor }};">
                                                {{ $jumlahPaparan }}
                                            </div>
                                        </div>

                                        <!-- INFO TABLE -->
                                        <table class="table table-sm table-borderless mb-1">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted">Tahun</td>
                                                    <td class="text-end fw-semibold">
                                                        <span class="badge"
                                                        style="background-color: {{ $accentColor }};">{{ $currentYear }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">PI No</td>
                                                    <td class="text-end fw-semibold">
                                                        <span class="badge"
                                                        style="background-color: {{ $accentColor }};">{{ $pi_no }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Sasaran</td>
                                                    <td class="text-end fw-semibold"><span class="badge"
                                                        style="background-color: {{ $accentColor }};">{{ $piTargetPaparan }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Kemaskini</td>
                                                    <td class="text-end fw-semibold">
                                                        <span class="badge"
                                                        style="background-color: {{ $accentColor }};">{{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') ?? '-' }}</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- BUTTONS -->
                                        <div class="mt-2 d-flex flex-nowrap justify-content-center gap-3">
                                            @if (!empty($item->doc_link))
                                                <a href="{{ $item->doc_link }}" target="_blank"
                                                    class="btn btn-sm btn-outline-secondary rounded-pill px-1 py-1"
                                                    style="font-size: 0.75rem;">
                                                    <i class="bx bxs-folder-open"></i> Shared Folder
                                                </a>
                                            @endif
                                            <a href="{{ route('dataptj.show', $item->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-1 py-1"
                                                style="font-size: 0.75rem;">
                                                <i class="bx bx-show"></i> Papar Maklumat
                                            </a>
                                        </div>

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
