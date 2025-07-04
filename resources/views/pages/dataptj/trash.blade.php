@extends('layouts.master')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Data JKEN</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('dataptj') }}"></i>Senarai Data JKEN</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Senarai Data JKEN Dipadam</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Data JKEN Dipadam</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped text-wrap text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pemilik Data</th>
                            <th>Tajuk Data</th>
                            <th>Shared Folder</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trashList as $dataptj)
                            <tr>
                                <td class="text-center">
                                        {{ ($trashList->currentPage() - 1) * $trashList->perPage() + $loop->iteration }}
                                    </td>
                                <td>{{ $dataptj->department->name }}</td>
                                <td>{{ $dataptj->nama_data ?? '-' }}</td>
                                <td class="text-center">
                                    @if (!empty($dataptj->doc_link))
                                        <a href="{{ $dataptj->doc_link }}" target="_blank" title="Shared Folder">
                                            <i class='bx bxs-folder-open' style="font-size: 1.2rem; color: #007bff;"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dataptj.restore', $dataptj->id) }}" class="btn btn-success btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembalikan">
                                        <i class="bx bx-undo"></i>
                                    </a>
                                    <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-bs-title="Padam">
                                        <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $dataptj->id }}"><i
                                                class="bx bx-trash"></i></span>
                                    </a>
                                </td>
                            </tr>

                            {{-- Sub-row Tahun --}}
                            <tr>
                                <td colspan="5" class="bg-light">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-secondary text-center align-middle">
                                            <tr>
                                                @foreach ($tahunList as $tahun)
                                                    <th style="background-color: #1b2b44; color: #fff">{{ $tahun->tahun }}
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <tr>
                                                @foreach ($tahunList as $tahun)
                                                    @php
                                                        $jumlah = $dataptj->jumlahs->firstWhere('tahun_id', $tahun->id);
                                                    @endphp
                                                    <td class="small">
                                                        @php
                                                            $jumlahPaparan = '-';
                                                            $sasaranPaparan = '-';

                                                            $jenis = $dataptj->jenis_nilai ?? 'Bilangan';

                                                            if ($jumlah && !is_null($jumlah->jumlah)) {
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
                                                            }

                                                            if ($jumlah && !is_null($jumlah->pi_target)) {
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
                                                            }
                                                        @endphp
                                                        @if ($jumlah)
                                                            <div><strong>KPI Universiti (BTU):</strong>
                                                                {{ $jumlah->is_kpi ? 'Ya' : '-' }}</div>
                                                            <div><strong>PI No.:</strong> {{ $jumlah->pi_no ?? '-' }}</div>
                                                            <div><strong>Sasaran:</strong> {{ $sasaranPaparan }}</div>
                                                            <div><strong>Pencapaian:</strong> {{ $jumlahPaparan }}</div>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif

                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tiada rekod</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                    <form action="{{ route('dataptj') }}" method="GET" id="perPageForm">
                        <select name="perPage" id="perPage" class="form-select"
                            onchange="document.getElementById('perPageForm').submit()">
                            <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                            <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <div class="mx-1 mt-2">{{ $trashList->firstItem() }} – {{ $trashList->lastItem() }} dari
                        {{ $trashList->total() }} rekod</div>
                    <div>{{ $trashList->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @foreach ($trashList as $dataptj)
        <div class="modal fade" id="deleteModal{{ $dataptj->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @isset($dataptj)
                            Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">
                                {{ ucfirst($dataptj->name) }}</span>?
                        @else
                            Tiada rekod
                        @endisset
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @isset($dataptj)
                            <form class="d-inline" method="POST"
                                action="{{ route('dataptj.forceDelete', $dataptj->id) }}">
                                {{ method_field('delete') }}
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-danger">Padam</button>
                            </form>
                        @endisset
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <!--end page wrapper -->
@endsection
