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
                    <li class="breadcrumb-item active" aria-current="page">Senarai Data JKEN</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('datautama.trash') }}">
                <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
            </a>
        </div>
    </div>
    <!--end breadcrumb-->
    <h6 class="mb-0 text-uppercase">Senarai Data JKEN</h6>
    <hr />
    <div class="card">
        <div class="card-body">
            <div class="d-lg-flex align-items-center mb-4 gap-3">
                <div class="position-relative">
                    <form action="{{ route('datautama.search') }}" method="GET" id="searchForm"
                        class="d-lg-flex align-items-center gap-3">
                        <div class="input-group">
                            <input type="text" class="form-control rounded" placeholder="Carian..." name="search"
                                value="{{ request('search') }}" id="searchInput">

                            <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                            <button type="submit" class="btn btn-primary ms-1 rounded" id="searchButton">
                                <i class="bx bx-search"></i>
                            </button>
                            <button type="button" class="btn btn-secondary ms-1 rounded" id="resetButton">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('datautama.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                        <i class="bx bxs-plus-square"></i> Tambah Data JKEN
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pemilik Data</th>
                            <th>Tajuk Data</th>
                            <th>Adakah ini KPI Universiti (BTU)?</th>
                            <th>No. PI</th>
                            <th>Sasaran PI</th>

                            @foreach ($tahunList as $tahun)
                                <th>{{ $tahun->tahun }}</th>
                            @endforeach
                            <th>Shared Folder</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($datautamaList as $datautama)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $datautama->department->name }}</td>
                                <td>{{ $datautama->jenisDataPtj->name ?? '-' }}</td>
                                <td>{{ $datautama->is_kpi ? 'Ya' : 'Tidak' }}</td>
                                <td>{{ $datautama->pi_no ?? '-' }}</td>
                                <td>{{ $datautama->pi_target ?? '-' }}</td>

                                @foreach ($tahunList as $tahun)
                                    @php
                                        $jumlah = $datautama->jumlahs->firstWhere('tahun_id', $tahun->id);
                                    @endphp
                                    <td>{{ $jumlah->jumlah ?? '-' }}</td>
                                @endforeach
                                <td class="text-center">
                                    @if (!empty($datautama->doc_link))
                                        <a href="{{ $datautama->doc_link }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Buka pautan shared folder">
                                            <i class='bx bxs-folder-open' style="font-size: 1.2rem; color: #007bff;"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('datautama.edit', $datautama->id) }}" class="btn btn-info btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kemaskini">
                                        <i class="bx bxs-edit"></i>
                                    </a>
                                    <a href="{{ route('datautama.show', $datautama->id) }}" class="btn btn-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        data-bs-title="Padam">
                                        <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $datautama->id }}"><i
                                                class="bx bx-trash"></i></span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 10 + $tahunList->count() }}" class="text-center">Tiada rekod</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                    <form action="{{ route('datautama.search') }}" method="GET" id="perPageForm"
                        class="d-flex align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="perPage" id="perPage" class="form-select form-select-sm"
                            onchange="document.getElementById('perPageForm').submit()">
                            <option value="10" {{ Request::get('perPage') == '10' ? 'selected' : '' }}>10</option>
                            <option value="20" {{ Request::get('perPage') == '20' ? 'selected' : '' }}>20</option>
                            <option value="30" {{ Request::get('perPage') == '30' ? 'selected' : '' }}>30</option>
                        </select>
                    </form>
                </div>

                <div class="d-flex justify-content-end align-items-center">
                    <span class="mx-2 mt-2 small text-muted">
                        Menunjukkan {{ $datautamaList->firstItem() }} hingga {{ $datautamaList->lastItem() }} daripada
                        {{ $datautamaList->total() }} rekod
                    </span>
                    <div class="pagination-wrapper">
                        {{ $datautamaList->appends([
                                'search' => request('search'),
                            ])->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @foreach ($datautamaList as $datautama)
        <div class="modal fade" id="deleteModal{{ $datautama->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @isset($datautama)
                            Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">
                                {{ ucfirst($datautama->name) }}</span>?
                        @else
                            Tiada rekod
                        @endisset
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @isset($datautama)
                            <form class="d-inline" method="POST" action="{{ route('datautama.destroy', $datautama->id) }}">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit the form on input change
            document.getElementById('searchInput').addEventListener('input', function() {
                document.getElementById('searchForm').submit();
            });

            // Reset form
            document.getElementById('resetButton').addEventListener('click', function() {
                // Redirect to the base route to clear query parameters
                window.location.href = "{{ route('datautama') }}";
            });
        });
    </script>
    <!--end page wrapper -->
@endsection
