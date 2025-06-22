@extends('layouts.master')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Tahun</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ route('tahun') }}">Senarai Tahun</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Senarai Tahun Dipadam
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <h6 class="mb-0 text-uppercase">Senarai Tahun Dipadam</h6>
    <hr />
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($trashList) > 0)
                            @foreach ($trashList as $tahun)
                                <tr>
                                    <td>{{ $tahun->tahun }}</td>
                                    <td>
                                        @if ($tahun->publish_status == 'Aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('tahun.restore', $tahun->id) }}" class="btn btn-success btn-sm"
                                            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembalikan">
                                            <i class="bx bx-undo"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                            data-bs-title="Padam">
                                            <span class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $tahun->id }}"><i
                                                    class="bx bx-trash"></i></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <td colspan="3">Tiada rekod</td>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($trashList as $tahun)
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal{{ $tahun->id }}" tabindex="-1" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Pengesahan Padam Rekod</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-text">
                            Adakah anda pasti ingin memadam kekal rekod <span style="font-weight: 600;">Tahun
                                {{ $tahun->tahun }}</span>?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <form class="d-inline" method="POST" action="{{ route('tahun.forceDelete', $tahun->id) }}">
                            {{ method_field('delete') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">Padam</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
