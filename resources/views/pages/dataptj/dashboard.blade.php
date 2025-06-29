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
                '#6D8BE4', // soft mid-blue
                '#F5B64D', // soft gold
                '#E27878', // soft coral red
                '#79C98B', // minty green
                '#B78ED3', // soft lilac purple
                '#F2A977', // peachy orange
                '#6CC3C1', // teal
                '#F5D76E', // warm yellow
                '#9DB3E1', // powder blue
                '#F5A3C3', // soft pink
                '#82C596', // pastel green
                '#D9A066', // muted amber
                '#A6A9E2', // lavender blue
                '#E2B66C', // camel gold
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

            <div class="mb-5">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-building" style="color: {{ $accentColor }};"></i>
                    <span class="ms-2 fw-semibold text-uppercase" style="color: {{ $accentColor }}; font-size: 1.1rem;">
                        {{ $departmentName }}
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

                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-5 h-100"
                                style="background: radial-gradient(circle at top left, {{ $lightAccent }}, #ffffff);
                                    box-shadow: inset 2px 2px 6px rgba(0,0,0,0.05), inset -2px -2px 6px rgba(255,255,255,0.8);">

                                <!-- HEADER FIXED HEIGHT WITH WRAPPING -->
                                <div class="d-flex align-items-center justify-content-center text-center px-2 py-2"
                                    style="background: linear-gradient(90deg, {{ $accentColor }} 0%, {{ $accentColor }}aa 100%);
            color: #fff;
            min-height: 70px;
            max-height: 90px;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            overflow-y: auto;
            white-space: normal;">
                                    <h6 class="fw-semibold text-uppercase mb-0"
                                        style="font-size: 0.95rem; line-height: 1.3;">
                                        {{ $item->nama_data ?? '-' }}
                                    </h6>
                                </div>

                                <div class="card-body d-flex flex-column p-4">

                                    <!-- VALUE -->
                                    <div class="text-center mb-3">
                                        <div class="fw-bold display-6" style="color: {{ $accentColor }}; opacity: 1;">
                                            {{ $jumlahPaparan }}
                                        </div>

                                    </div>

                                    <!-- INFO TABLE -->
                                    <table class="table table-sm table-borderless mb-3">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted">Tahun</td>
                                                <td class="text-end fw-semibold">{{ $currentYear }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">PI No</td>
                                                <td class="text-end fw-semibold">{{ $pi_no }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Sasaran</td>
                                                <td class="text-end fw-semibold">{{ $piTargetPaparan }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Kemaskini</td>
                                                <td class="text-end fw-semibold">
                                                    {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : $item->created_at->format('d/m/Y') ?? '-' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- BUTTONS -->
                                    <div class="mt-2 d-flex flex-wrap justify-content-between gap-2">
                                        @if (!empty($item->doc_link))
                                            <a href="{{ $item->doc_link }}" target="_blank"
                                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                                <i class="bx bxs-folder-open"></i> Shared Folder
                                            </a>
                                        @endif
                                        <a href="{{ route('dataptj.show', $item->id) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill">
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
