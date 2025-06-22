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
                    <li class="breadcrumb-item"><a href="{{ route('datautama') }}"></i>Senarai Data JKEN</a>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pemilik Data</th>
                            <th>Tajuk Data</th>
                            <th>Adakah ini KPI?</th>
                            <th>No. PI</th>
                            <th>Sasaran PI</th>

                            @foreach ($tahunList as $tahun)
                                <th>{{ $tahun->tahun }}</th>
                            @endforeach
                            <th>Pautan Simpanan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trashList as $datautama)
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
                                <td>
                                    @if (!empty($datautama->doc_link))
                                        <a href="{{ $datautama->doc_link }}" target="_blank">Pautan</a>
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
                    <form action="{{ route('datautama') }}" method="GET" id="perPageForm">
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
    @foreach ($trashList as $datautama)
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
                            <form class="d-inline" method="POST"
                                action="{{ route('datautama.forceDelete', $datautama->id) }}">
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
