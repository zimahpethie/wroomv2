@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Data PTJ</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dataptj') }}">Senarai Data PTJ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Maklumat {{ ucfirst($dataptj->nama_data) }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('dataptj.edit', $dataptj->id) }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Kemaskini Maklumat</button>
            </a>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">Maklumat {{ ucfirst($dataptj->nama_data) }}</h6>
    <hr />

    <div class="row">
        <div class="col-lg-12">
            @if (count($perbandinganByYear) > 0)
                <div class="card mt-4">
                    <div class="card-body">
                        <canvas id="jumlahByYearChart" height="80"></canvas>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Information Table -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Bahagian / Unit</th>
                            <td>{{ $dataptj->department->name }}</td>
                        </tr>
                        <tr>
                            <th>Tajuk Data</th>
                            <td>{{ $dataptj->nama_data ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Shared Folder</th>
                            <td>
                                @if (!empty($dataptj->doc_link))
                                    <a href="{{ $dataptj->doc_link }}" target="_blank" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Buka pautan shared folder">
                                        <i class='bx bxs-folder-open' style="font-size: 1.2rem; color: #007bff;"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah / Bilangan / Peratus / Pencapaian Mengikut Tahun</th>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr class="text-center">
                                                <th style="width:8%">Tahun</th>
                                                <th style="width:12%">KPI Universiti (BTU)</th>
                                                <th style="width:25%">No. PI</th>
                                                <th style="width:20%">Sasaran</th>
                                                <th style="width:20%">Pencapaian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tahunList as $tahun)
                                                @php
                                                    $jumlah = $dataptj->jumlahs->firstWhere('tahun_id', $tahun->id);
                                                @endphp
                                                @if ($jumlah)
                                                    @php
                                                        $jenis = $dataptj->jenis_nilai ?? 'Bilangan';

                                                        if (!is_null($jumlah->jumlah)) {
                                                            if ($jenis == 'Peratus') {
                                                                $jumlahPaparan = $jumlah->jumlah . ' %';
                                                            } elseif ($jenis == 'Mata Wang') {
                                                                $jumlahPaparan =
                                                                    'RM ' . number_format($jumlah->jumlah, 2);
                                                            } elseif ($jenis == 'Bilangan') {
                                                                $jumlahPaparan = number_format(
                                                                    (int) $jumlah->jumlah,
                                                                    0,
                                                                    '.',
                                                                    ',');
                                                            } else {
                                                                $jumlahPaparan = $jumlah->jumlah;
                                                            }
                                                        } else {
                                                            $jumlahPaparan = '-';
                                                        }

                                                        if (!is_null($jumlah->pi_target)) {
                                                            if ($jenis == 'Peratus') {
                                                                $sasaranPaparan = $jumlah->pi_target . ' %';
                                                            } elseif ($jenis == 'Mata Wang') {
                                                                $sasaranPaparan =
                                                                    'RM ' . number_format($jumlah->pi_target, 2);
                                                            } elseif ($jenis == 'Bilangan') {
                                                                $sasaranPaparan = number_format(
                                                                    (int) $jumlah->pi_target,
                                                                    0,
                                                                    '.',
                                                                    ',');
                                                            } else {
                                                                $sasaranPaparan = $jumlah->pi_target;
                                                            }
                                                        } else {
                                                            $sasaranPaparan = '-';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center fw-semibold">{{ $tahun->tahun }}</td>
                                                        <td class="text-center">{{ $jumlah->is_kpi ? 'Ya' : '-' }}</td>
                                                        <td class="text-wrap">{{ $jumlah->pi_no ?? '-' }}</td>
                                                        <td class="text-end fw-semibold">{{ $sasaranPaparan }}</td>
                                                        <td class="text-end fw-semibold">{{ $jumlahPaparan }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </th>
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
                            <td>{{ $dataptj->creator->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tarikh Cipta</th>
                            <td>{{ $dataptj->created_at ? $dataptj->created_at->format('d/m/Y h:i A') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dikemaskini oleh</th>
                            <td>{{ $dataptj->updater->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tarikh Kemaskini</th>
                            <td>{{ $dataptj->updated_at ? $dataptj->updated_at->format('d/m/Y h:i A') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- End Information Table -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('jumlahByYearChart').getContext('2d');
            var dataFromServer = @json($perbandinganByYear);

            var labels = Object.keys(dataFromServer);
            var jumlahData = labels.map(year => dataFromServer[year].jumlah);
            var piTargetData = labels.map(year => dataFromServer[year].pi_target);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sasaran',
                        data: piTargetData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Pencapaian',
                        data: jumlahData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                padding: 10,
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: [
                                @json(strtoupper($dataptj->nama_data))
                            ],
                            font: {
                                size: 14,
                                weight: 'bold',
                            },
                            color: '#000',
                            padding: {
                                top: 10,
                                bottom: 45
                            }
                        },
                        tooltip: {
                            enabled: true
                        },
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                },
                                padding: 20
                            }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        var ctx = chart.ctx;
                        chart.data.datasets.forEach(function(dataset, i) {
                            var meta = chart.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var value = dataset.data[index];
                                if (value !== null && value !== 0) {
                                    ctx.fillStyle = '#000';
                                    ctx.font = 'bold 11px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillText(value, bar.x, bar.y - 5);
                                }
                            });
                        });
                    }
                }],
            });
        });
    </script>

    <!-- End Page Wrapper -->
@endsection
