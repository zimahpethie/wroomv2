@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        <!-- PAGE TITLE + FILTER INLINE -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0">DATA WAR ROOM DASHBOARD</h3>
            </div>
            <form id="dashboardFilter" action="{{ route('dataptj.dashboard') }}" method="GET"
                class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">📌 Semua Bahagian</option>
                    @foreach ($departmentList as $department)
                        <option value="{{ $department->id }}"
                            {{ $selectedDepartment == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <button id="resetButton" type="button" class="btn btn-sm btn-outline-dark">Reset</button>
            </form>
        </div>

        @php
            $currentYear = now()->year;
            $colorPalette = [
                '#0d6efd',
                '#6610f2',
                '#6f42c1',
                '#d63384',
                '#dc3545',
                '#fd7e14',
                '#ffc107',
                '#198754',
                '#20c997',
                '#0dcaf0',
                '#6c757d',
                '#343a40',
                '#4b0082',
                '#116466',
            ];
            $departmentColors = [];
            $i = 0;
            foreach ($departmentList as $dept) {
                $departmentColors[$dept->id] = $colorPalette[$i] ?? '#6c757d';
                $i++;
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
                <div class="px-2 py-2 mb-3 rounded" style="background: {{ $gradient }}; color: #fff;">
                    <h4 class="fw-bold m-0"><i class="bi bi-building"></i> {{ strtoupper($departmentName) }}</h4>
                </div>
                <div class="row g-4">
                    @foreach ($dataItems as $item)
                        @php
                            $jumlahRecord = $item->jumlahs->firstWhere('tahun.tahun', $currentYear);
                            $jumlah = $jumlahRecord->jumlah ?? null;
                            $pi_no = $jumlahRecord->pi_no ?? '-';
                            $pi_target = $jumlahRecord->pi_target ?? null;
                            $jenis = $item->jenis_nilai ?? 'Bilangan';

                            if (!is_null($jumlah)) {
                                if ($jenis === 'Peratus') {
                                    $jumlahPaparan = $jumlah . ' %';
                                } elseif ($jenis === 'Mata Wang') {
                                    $jumlahPaparan = 'RM ' . number_format($jumlah, 2);
                                } else {
                                    $jumlahPaparan = $jumlah;
                                }
                            } else {
                                $jumlahPaparan = '-';
                            }

                            if (!is_null($pi_target)) {
                                if ($jenis === 'Peratus') {
                                    $piTargetPaparan = $pi_target . ' %';
                                } elseif ($jenis === 'Mata Wang') {
                                    $piTargetPaparan = 'RM ' . number_format($pi_target, 2);
                                } else {
                                    $piTargetPaparan = $pi_target;
                                }
                            } else {
                                $piTargetPaparan = '-';
                            }
                        @endphp

                        <!-- CARD DESIGN -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card shadow border-0 h-100 rounded-4 overflow-hidden"
                                style="background-color: {{ $headerColor }}; color: #fff;">
                                <div class="card-body d-flex flex-column justify-content-between">

                                    <!-- NAMA DATA -->
                                    <div class="text-center mb-1">
                                        <div
                                            style="
        text-transform: uppercase;
        font-size: 0.95rem;
        font-weight: 600;
        opacity: 0.95;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    ">
                                            {{ $item->nama_data ?? '-' }}
                                        </div>
                                    </div>

                                    <!-- GARIS PEMISAH -->
                                    <div class="mx-auto"
                                        style="width: 50%; height: 1px; background-color: rgba(255,255,255,0.3); margin-bottom: 0.75rem;">
                                    </div>

                                    <!-- NILAI UTAMA BESAR CENTER -->
                                    <div class="text-center mb-3">
                                        <div class="fw-bold display-5" style="letter-spacing: 1px;">
                                            {{ $jumlahPaparan }}
                                        </div>
                                    </div>

                                    <!-- INFO DETAIL TABLE -->
                                    <div class="mb-3 px-1">
                                        <table class="table table-borderless mb-0" style="color: #fff;">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-normal">Tahun</td>
                                                    <td class="fw-semibold text-end">{{ $currentYear }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-normal">PI No</td>
                                                    <td class="fw-semibold text-end">{{ $pi_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-normal">Sasaran</td>
                                                    <td class="fw-semibold text-end">{{ $piTargetPaparan }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-normal">Kemaskini</td>
                                                    <td class="fw-semibold text-end">
                                                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') ?? '-' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- BUTTONS -->
                                    <div class="mt-auto d-flex justify-content-between align-items-center gap-2">
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
