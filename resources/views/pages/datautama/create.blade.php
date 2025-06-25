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
                        <input type="text" class="form-control bg-light" value="Tiada" readonly>
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
                    <label for="jenis_data_ptj_id" class="form-label">Tajuk Data</label>
                    <select class="form-select {{ $errors->has('jenis_data_ptj_id') ? 'is-invalid' : '' }}"
                        name="jenis_data_ptj_id" required>
                        <option value="">-- Pilih Tajuk Data --</option>
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

                {{-- Jenis Nilai Data --}}
                <div class="mb-3">
                    <label class="form-label d-block">Jenis Nilai Data</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_nilai" id="jenis_bilangan"
                            value="Bilangan" {{ old('jenis_nilai') == 'Bilangan' ? 'checked' : '' }}>
                        <label class="form-check-label" for="jenis_bilangan">Bilangan</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_nilai" id="jenis_peratus" value="Peratus"
                            {{ old('jenis_nilai') == 'Peratus' ? 'checked' : '' }}>
                        <label class="form-check-label" for="jenis_peratus">Peratus (%)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_nilai" id="jenis_rm" value="Mata Wang"
                            {{ old('jenis_nilai') == 'Mata Wang' ? 'checked' : '' }}>
                        <label class="form-check-label" for="jenis_rm">Mata Wang (RM)</label>
                    </div>

                    @if ($errors->has('jenis_nilai'))
                        <div class="invalid-feedback d-block">
                            @foreach ($errors->get('jenis_nilai') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Input Jumlah Mengikut Tahun --}}
                <hr />
                <h6 class="text-uppercase mt-4">Perbandingan Jumlah / Bilangan / Peratus / Pencapaian Bagi Data Berkenaan
                    Mengikut Tahun (Jika Ada)</h6>

                @foreach ($tahunList as $tahun)
                    @php
                        $tahunKey = $tahun->id ?? 'year_' . $tahun->tahun;
                    @endphp

                    <div class="border p-3 mb-3 rounded bg-light-subtle">
                        <strong>{{ $tahun->tahun }}</strong>
                        <div class="row mt-2">
                            {{-- is_kpi --}}
                            <div class="col-md-3">
                                <label class="form-label d-block">Adakah ini KPI Universiti (BTU)?</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input is-kpi-radio" type="radio"
                                        name="is_kpi[{{ $tahunKey }}]" id="kpi_{{ $tahunKey }}_1" value="1"
                                        {{ old('is_kpi.' . $tahunKey) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kpi_{{ $tahunKey }}_1">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input is-kpi-radio" type="radio"
                                        name="is_kpi[{{ $tahunKey }}]" id="kpi_{{ $tahunKey }}_0" value="0"
                                        {{ old('is_kpi.' . $tahunKey, '0') == '0' ? 'checked' : '' }}>
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

                            {{-- pi_no --}}
                            <div class="col-md-3">
                                <label class="form-label">No. PI</label>
                                <input type="text"
                                    class="form-control {{ $errors->has('pi_no.' . $tahunKey) ? 'is-invalid' : '' }}"
                                    name="pi_no[{{ $tahunKey }}]" value="{{ old('pi_no.' . $tahunKey) }}">
                                @if ($errors->has('pi_no.' . $tahunKey))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('pi_no.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- pi_target --}}
                            <div class="col-md-3">
                                <label class="form-label">Sasaran</label>
                                <input type="number" step="0.01"
                                    class="form-control {{ $errors->has('pi_target.' . $tahunKey) ? 'is-invalid' : '' }}"
                                    name="pi_target[{{ $tahunKey }}]" value="{{ old('pi_target.' . $tahunKey) }}">
                                @if ($errors->has('pi_target.' . $tahunKey))
                                    <div class="invalid-feedback">
                                        @foreach ($errors->get('pi_target.' . $tahunKey) as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- jumlah --}}
                            <div class="col-md-3">
                                <label class="form-label">Pencapaian</label>
                                <input type="number" step="0.01"
                                    class="form-control {{ $errors->has('jumlah.' . $tahunKey) ? 'is-invalid' : '' }}"
                                    name="jumlah[{{ $tahunKey }}]" value="{{ old('jumlah.' . $tahunKey) }}">
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

                {{-- Doc Link --}}
                <div class="mb-3">
                    <label for="doc_link" class="form-label">Shared Folder</label>
                    <span data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Sila letak pautan url shared folder"><input type="url"
                            class="form-control {{ $errors->has('doc_link') ? 'is-invalid' : '' }}" name="doc_link"
                            value="{{ old('doc_link') }}"></span>
                    @if ($errors->has('doc_link'))
                        <div class="invalid-feedback">
                            @foreach ($errors->get('doc_link') as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
            </form>
        </div>
    </div>
    <!-- End Page Wrapper -->
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
