@extends('layouts.master')

@section('content')
    <!-- Breadcrumb -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Pengurusan Data Utama</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datautama') }}">Senarai Data Utama</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Data Utama</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <h6 class="mb-0 text-uppercase">{{ $str_mode }} Data Utama</h6>
    <hr />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $save_route }}">
                {{ csrf_field() }}

                <div class="mb-3">
                    <label class="form-label">Bahagian / Unit</label>
                    <input type="text" class="form-control" value="{{ $dataUtama->department->name ?? '-' }}" readonly>
                    <input type="hidden" name="department_id" value="{{ $dataUtama->department_id }}">
                </div>

                {{-- Subunit --}}
                <div class="mb-3">
                    <label for="subunit_id" class="form-label">Sub Unit</label>

                    @if ($subunitList->isEmpty())
                        <input type="text" class="form-control bg-light" value="Tiada" readonly>
                        <input type="hidden" name="subunit_id" value="">
                    @else
                        <select class="form-select {{ $errors->has('subunit_id') ? 'is-invalid' : '' }}" name="subunit_id">
                            <option value="">-- Pilih Sub Unit --</option>
                            @foreach ($subunitList as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('subunit_id', $dataUtama->subunit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('subunit_id'))
                            <div class="invalid-feedback">{{ $errors->first('subunit_id') }}</div>
                        @endif
                    @endif
                </div>

                {{-- Jenis Data --}}
                <div class="mb-3">
                    <label for="jenis_data_ptj_id" class="form-label">Tajuk Data</label>
                    <select class="form-select {{ $errors->has('jenis_data_ptj_id') ? 'is-invalid' : '' }}"
                        name="jenis_data_ptj_id" required>
                        @foreach ($jenisDataList as $data)
                            <option value="{{ $data->id }}"
                                {{ old('jenis_data_ptj_id', $dataUtama->jenis_data_ptj_id) == $data->id ? 'selected' : '' }}>
                                {{ $data->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('jenis_data_ptj_id'))
                        <div class="invalid-feedback">{{ $errors->first('jenis_data_ptj_id') }}</div>
                    @endif
                </div>

                {{-- Doc Link --}}
                <div class="mb-3">
                    <label for="doc_link" class="form-label">Pautan Dokumen</label>
                    <input type="url" class="form-control {{ $errors->has('doc_link') ? 'is-invalid' : '' }}"
                        name="doc_link" value="{{ old('doc_link', $dataUtama->doc_link) }}">
                    @if ($errors->has('doc_link'))
                        <div class="invalid-feedback">{{ $errors->first('doc_link') }}</div>
                    @endif
                </div>

                {{-- Input Jumlah Mengikut Tahun --}}
                <hr />
                <h6 class="text-uppercase mt-4">Perbandingan Jumlah / Bilangan / Peratus / Pencapaian Bagi Data Berkenaan
                    Mengikut Tahun (Jika Ada)</h6>

                @foreach ($tahunList as $tahun)
                    @php
                        $tahunKey = $tahun->id;
                        $jumlah = old("jumlah.$tahunKey", $jumlahArray[$tahunKey]['jumlah'] ?? '');
                        $isKpi = old("is_kpi.$tahunKey", $jumlahArray[$tahunKey]['is_kpi'] ?? '0');
                        $piNo = old("pi_no.$tahunKey", $jumlahArray[$tahunKey]['pi_no'] ?? '');
                        $piTarget = old("pi_target.$tahunKey", $jumlahArray[$tahunKey]['pi_target'] ?? '');
                    @endphp

                    <div class="border p-3 mb-3 rounded bg-light-subtle">
                        <strong>{{ $tahun->tahun }}</strong>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label class="form-label d-block">Adakah ini KPI Universiti (BTU)?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input is-kpi-radio" type="radio"
                                        name="is_kpi[{{ $tahunKey }}]" id="kpi_{{ $tahunKey }}_1" value="1"
                                        {{ $isKpi == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kpi_{{ $tahunKey }}_1">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input is-kpi-radio" type="radio"
                                        name="is_kpi[{{ $tahunKey }}]" id="kpi_{{ $tahunKey }}_0" value="0"
                                        {{ $isKpi == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kpi_{{ $tahunKey }}_0">Tidak</label>
                                </div>
                                @if ($errors->has('is_kpi.' . $tahunKey))
                                    <div class="invalid-feedback d-block">
                                        @foreach ($errors->get('is_kpi.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">No. PI</label>
                                <input type="text" class="form-control" name="pi_no[{{ $tahunKey }}]"
                                    value="{{ $piNo }}">
                                @if ($errors->has('pi_no.' . $tahunKey))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('pi_no.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Sasaran</label>
                                <input type="number" step="0.01" class="form-control"
                                    name="pi_target[{{ $tahunKey }}]" value="{{ $piTarget }}">
                                @if ($errors->has('pi_target.' . $tahunKey))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('pi_target.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" step="0.01" class="form-control"
                                    name="jumlah[{{ $tahunKey }}]" value="{{ $jumlah }}">
                                @if ($errors->has('jumlah.' . $tahunKey))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('jumlah.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
            </form>
        </div>
    </div>

    <script>
        function togglePIFieldsForYear(tahunKey) {
            const kpiYes = document.getElementById(`kpi_${tahunKey}_1`);
            const piNoInput = document.querySelector(`input[name="pi_no[${tahunKey}]"]`);

            if (!kpiYes || !piNoInput) return;

            if (kpiYes.checked) {
                piNoInput.removeAttribute("readonly");
                piNoInput.classList.remove("bg-light");
            } else {
                piNoInput.setAttribute("readonly", true);
                piNoInput.value = "";
                piNoInput.classList.add("bg-light");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('.is-kpi-radio');
            radios.forEach(function(radio) {
                const tahunKey = radio.name.match(/is_kpi\[(.*?)\]/)[1];
                radio.addEventListener("change", function() {
                    togglePIFieldsForYear(tahunKey);
                });
                togglePIFieldsForYear(tahunKey); // run on load
            });
        });
    </script>
@endsection
