@extends('layouts.master')
@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Tahun</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Senarai Tahun</li>
            </ol>
        </nav>
    </div>
    @role('Superadmin')
    <div class="ms-auto">
        <a href="{{ route('tahun.trash') }}">
            <button type="button" class="btn btn-primary mt-2 mt-lg-0">Senarai Rekod Dipadam</button>
        </a>
    </div>
    @endrole
</div>
<!--end breadcrumb-->
<h6 class="mb-0 text-uppercase">Senarai Tahun</h6>
<hr />
<div class="card">
    <div class="card-body">
        <div class="d-lg-flex align-items-center mb-4 gap-3">
            <div class="position-relative">
                <form action="{{ route('tahun.search') }}" method="GET" id="searchForm"
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
            @hasanyrole('Superadmin|Admin')
            <div class="ms-auto">
                <a href="{{ route('tahun.create') }}" class="btn btn-primary radius-30 mt-2 mt-lg-0">
                    <i class="bx bxs-plus-square"></i> Tambah Tahun
                </a>
            </div>
            @endhasanyrole
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tahun</th>
                        <th>Status</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($tahunList) > 0)
                    @foreach ($tahunList as $tahun)
                    <tr>
                        <td>
                                        {{ ($tahunList->currentPage() - 1) * $tahunList->perPage() + $loop->iteration }}
                                    </td>
                        <td>{{ $tahun->tahun }}</td>
                        <td>
                            @if ($tahun->publish_status == 'Aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @hasanyrole('Superadmin|Admin')
                            <a href="{{ route('tahun.edit', $tahun->id) }}" class="btn btn-info btn-sm"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kemaskini">
                                <i class="bx bxs-edit"></i>
                            </a>
                            @endhasanyrole
                            <a href="{{ route('tahun.show', $tahun->id) }}" class="btn btn-primary btn-sm"
                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Papar">
                                <i class="bx bx-show"></i>
                            </a>
                            @hasanyrole('Superadmin|Admin')
                            <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                data-bs-title="Padam">
                                <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $tahun->id }}"><i
                                        class="bx bx-trash"></i></span>
                            </a>
                            @endhasanyrole


                        </td>
                    </tr>
                    @endforeach
                    @else
                    <td colspan="4">Tiada rekod</td>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <span class="mr-2 mx-1">Jumlah rekod per halaman</span>
                <form action="{{ route('tahun.search') }}" method="GET" id="perPageForm"
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
                    Menunjukkan {{ $tahunList->firstItem() }} hingga {{ $tahunList->lastItem() }} daripada
                    {{ $tahunList->total() }} rekod
                </span>
                <div class="pagination-wrapper">
                    {{ $tahunList->appends([
                                'search' => request('search'),
                            ])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@foreach ($tahunList as $tahun)
<div class="modal fade" id="deleteModal{{ $tahun->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @isset($tahun)
                Adakah anda pasti ingin memadam rekod <span style="font-weight: 600;">Tahun
                    {{ $tahun->name }}</span>?
                @else
                Error: tahun data not available.
                @endisset
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                @isset($tahun)
                <form class="d-inline" method="POST" action="{{ route('tahun.destroy', $tahun->id) }}">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit the form on input change
        document.getElementById('searchInput').addEventListener('input', function() {
            document.getElementById('searchForm').submit();
        });

        // Reset form
        document.getElementById('resetButton').addEventListener('click', function() {
            // Redirect to the base route to clear query parameters
            window.location.href = "{{ route('tahun') }}";
        });
    });
</script>
@endsection