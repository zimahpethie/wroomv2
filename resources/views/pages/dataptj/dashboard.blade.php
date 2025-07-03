@extends('layouts.master')

@section('content')
    <div class="container-fluid">

        <!-- PAGE TITLE + FILTER INLINE -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h2 class="fw-bold text-primary mb-3 mb-md-0 d-flex align-items-center flex-wrap" style="font-size: 1.8rem;">
                DATA WAR ROOM {{ $selectedYear }}
            </h2>
            @hasanyrole('Superadmin|Admin')
                <form id="dashboardFilter" action="{{ route('dataptj.dashboard') }}" method="GET"
                    class="d-flex flex-row flex-wrap align-items-center gap-2">
                    <div>
                        <select name="year" class="form-select form-select-sm rounded-pill shadow-sm"
                            onchange="this.form.submit()">
                            @foreach ($tahunList as $tahun)
                                <option value="{{ $tahun->tahun }}" {{ $selectedYear == $tahun->tahun ? 'selected' : '' }}>
                                    📅 {{ $tahun->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
            @endhasanyrole
        </div>

        @php
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
                            $jumlahRecord = $item->jumlahs->firstWhere('tahun.tahun', $selectedYear);
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
                                    <div class="d-flex align-items-center justify-content-center text-center px-2 py-2"
                                        style="background-color: {{ $accentColor }}33;">
                                        <h6 class="fw-bold text-uppercase mb-0 text-wrap text-break"
                                            style="font-size: clamp(0.75rem, 2.5vw, 1rem); color: {{ $accentColor }};">
                                            {{ $item->nama_data ?? '-' }}
                                        </h6>
                                    </div>

                                    <div class="card-body d-flex flex-column p-3">

                                        <!-- VALUE OR METER -->
                                        @php
                                            if (is_numeric($pi_target) && $pi_target > 0 && is_numeric($jumlah)) {
                                                $progress = min(100, round(($jumlah / $pi_target) * 100));
                                            } else {
                                                $progress = 0;
                                            }
                                            $chartId = 'barChart_' . $item->id;
                                        @endphp

                                        <div class="my-2" style="width: 100%; max-width: 400px; margin:auto;">
                                            <canvas id="{{ $chartId }}"></canvas>
                                        </div>

                                        <!-- INFO TABLE -->
                                        <table class="table table-sm table-bordered mb-1">
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted">Tahun</td>
                                                    <td class="fw-semibold small">{{ $selectedYear }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">PI No</td>
                                                    <td class="fw-semibold small">{{ $pi_no }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">Sasaran</td>
                                                    <td class="fw-semibold small">{{ $piTargetPaparan }}
                                                    </td>
                                                </tr>
                                                @if ($pi_target && $pi_target > 0)
                                                    <tr>
                                                        <td class="text-muted">Pencapaian</td>
                                                        <td class="fw-semibold small">{{ $jumlahPaparan }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="text-muted">Kemaskini</td>
                                                    <td class="fw-semibold small">
                                                        {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : ($item->created_at ? $item->created_at->format('d/m/Y H:i') : '-') }}

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
            url.searchParams.delete('year');
            window.location.href = url.toString();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Build array of data
            const barChartsData = [
                @foreach ($dataList as $dataItems)
                    @foreach ($dataItems as $item)
                        @php
                            $jumlahRecord = $item->jumlahs->firstWhere('tahun.tahun', $selectedYear);
                            $jumlah = isset($jumlahRecord->jumlah) ? $jumlahRecord->jumlah : 'null';
                            $pi_target = isset($jumlahRecord->pi_target) ? $jumlahRecord->pi_target : 'null';
                            $accentColor = $departmentColors[$item->department_id] ?? '#6c757d';
                            $chartId = 'barChart_' . $item->id;
                        @endphp {
                            id: '{{ $chartId }}',
                            label: @json($item->nama_data ?? '-'),
                            jumlah: {{ $jumlah }},
                            pi_target: {{ $pi_target }},
                            accentColor: '{{ $accentColor }}'
                        },
                    @endforeach
                @endforeach
            ];

            // Render each chart
            barChartsData.forEach(item => {
                const ctx = document.getElementById(item.id);
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Sasaran', 'Pencapaian'],
                        datasets: [{
                            label: item.label,
                            data: [item.pi_target, item.jumlah],
                            backgroundColor: [
                                '#bdc3c7',
                                item.accentColor
                            ],
                            borderColor: [
                                '#bdc3c7',
                                item.accentColor
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        layout: {
                            padding: {
                                top: 20
                            }
                        },
                        plugins: {
                            title: {
                                display: false,
                                text: item.label,
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                color: '#000'
                            },
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return `${tooltipItem.label}: ${tooltipItem.raw}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    },
                    plugins: [{
                        afterDraw: function(chart) {
                            var ctx = chart.ctx;
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.fillStyle = '#000';
                            ctx.font = 'bold 12px Arial';
                            chart.data.datasets.forEach((dataset, i) => {
                                var meta = chart.getDatasetMeta(i);
                                meta.data.forEach((bar, index) => {
                                    var data = dataset.data[index];
                                    ctx.fillText(data, bar.x, bar.y -
                                        5);
                                });
                            });
                        }
                    }]
                });

            });
        });
    </script>
@endsection
