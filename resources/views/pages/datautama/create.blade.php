@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Data JKEN</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datautama') }}">Senarai Data JKEN</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Data JKEN</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">{{ $str_mode }} Data JKEN</h6>
    <hr />

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ $save_route }}">
                {{ csrf_field() }}

                <div class="mb-3">
                    <label class="form-label">Bahagian / Unit</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->department->name ?? '-' }}"
                        readonly>
                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                </div>

                {{-- Subunit --}}
                <div class="mb-3">
                    <label for="subunit_id" class="form-label">Sub Unit</label>

                    @if ($subunitList->isEmpty())
                        <input type="text" class="form-control bg-light" value="Tiada subunit" readonly>
                        <input type="hidden" name="subunit_id" value="">
                    @else
                        <select class="form-select {{ $errors->has('subunit_id') ? 'is-invalid' : '' }}" name="subunit_id">
                            <option value="">-- Pilih Sub Unit --</option>
                            @foreach ($subunitList as $unit)
                                <option value="{{ $unit->id }}" {{ old('subunit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('subunit_id'))
                            <div class="invalid-feedback">
                                @foreach ($errors->get('subunit_id') as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Jenis Data --}}
                <div class="mb-3">
                    <label for="jenis_data_ptj_id" class="form-label">Nama Data</label>
                    <select class="form-select {{ $errors->has('jenis_data_ptj_id') ? 'is-invalid' : '' }}"
                        name="jenis_data_ptj_id" required>
                        <option value="">-- Pilih Nama Data --</option>
                        @foreach ($jenisDataList as $data)
                            <option value="{{ $data->id }}"
                                {{ old('jenis_data_ptj_id') == $data->id ? 'selected' : '' }}>
                                {{ $data->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('jenis_data_ptj_id'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('jenis_data_ptj_id') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    {{-- KPI --}}
                    <div class="col-md-4">
                        <label class="form-label">Adakah ini KPI?</label>
                        <div>
                            <label><input type="radio" name="is_kpi" value="1"
                                    {{ old('is_kpi') == '1' ? 'checked' : '' }}> Ya</label>
                            &nbsp;
                            <label><input type="radio" name="is_kpi" value="0"
                                    {{ old('is_kpi', '0') == '0' ? 'checked' : '' }}> Tidak</label>
                        </div>
                        @if ($errors->has('is_kpi'))
                            <div class="invalid-feedback d-block">
                                @foreach ($errors->get('is_kpi') as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- PI No --}}
                    <div class="col-md-4">
                        <label for="pi_no" class="form-label">No. PI</label>
                        <input type="text" class="form-control" name="pi_no" id="pi_no"
                            value="{{ old('pi_no') }}">
                        @if ($errors->has('pi_no'))
                            <div class="invalid-feedback d-block">
                                @foreach ($errors->get('pi_no') as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- PI Target --}}
                    <div class="col-md-4">
                        <label for="pi_target" class="form-label">PI Target</label>
                        <input type="number" step="0.01" class="form-control" name="pi_target" id="pi_target"
                            value="{{ old('pi_target') }}">
                        @if ($errors->has('pi_target'))
                            <div class="invalid-feedback d-block">
                                @foreach ($errors->get('pi_target') as $error)
                                    {{ $error }}
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>



                {{-- Doc Link --}}
                <div class="mb-3">
                    <label for="doc_link" class="form-label">Pautan Dokumen</label>
                    <input type="url" class="form-control {{ $errors->has('doc_link') ? 'is-invalid' : '' }}"
                        name="doc_link" value="{{ old('doc_link') }}">
                    @if ($errors->has('doc_link'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('doc_link') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Input Jumlah Mengikut Tahun --}}
                <hr />
                <h6 class="text-uppercase mt-4">Isi Jumlah Mengikut Tahun</h6>

                <div class="row">
                    @foreach ($tahunList as $tahun)
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ $tahun->tahun }}</label>
                            <input type="number" step="0.01" class="form-control"
                                name="jumlah[{{ $tahun->id ?? 'year_' . $tahun->tahun }}]"
                                value="{{ old('jumlah.' . ($tahun->id ?? 'year_' . $tahun->tahun)) }}">
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
            </form>
        </div>
    </div>
    <!-- End Page Wrapper -->
    <script>
        function togglePIFields() {
            const isKPI = document.querySelector('input[name="is_kpi"]:checked')?.value;
            const piFields = ['pi_no', 'pi_target'];

            piFields.forEach(id => {
                const el = document.getElementById(id);
                if (isKPI === '1') {
                    el.removeAttribute('readonly');
                    el.classList.remove('bg-light');
                } else {
                    el.setAttribute('readonly', true);
                    el.value = '';
                    el.classList.add('bg-light');
                }
            });
        }

        document.querySelectorAll('input[name="is_kpi"]').forEach(el => {
            el.addEventListener('change', togglePIFields);
        });

        togglePIFields(); // Run on page load
    </script>
@endsection
