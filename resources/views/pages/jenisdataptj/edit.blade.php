@extends('layouts.master')

@section('content')
<!-- Breadcrumb -->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Pengurusan Jenis Data PTJ</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bx bx-home-alt"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('subunit') }}">Senarai Jenis Data PTJ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $str_mode }} Jenis Data PTJ</li>
            </ol>
        </nav>
    </div>
</div>
<!-- End Breadcrumb -->

<h6 class="mb-0 text-uppercase">{{ $str_mode }} Jenis Data PTJ</h6>
<hr />

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $save_route }}">
            {{ csrf_field() }}

            <div class="mb-3">
                <label for="department_id" class="form-label">Bahagian / Unit</label>
                <select class="form-select {{ $errors->has('department_id') ? 'is-invalid' : '' }}" id="department_id" name="department_id">
                    @foreach ($departmentList as $department)
                    <option value="{{ $department->id }}"
                        {{ old('department_id') == $department->id || ($jenisdataptj->department_id ?? '') == $department->id ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                    @endforeach
                </select>
                @if ($errors->has('department_id'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('department_id') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="subunit_id" class="form-label">Sub Unit</label>
                <select class="form-select {{ $errors->has('subunit_id') ? 'is-invalid' : '' }}" id="subunit_id" name="subunit_id">
                    @foreach ($subunitList as $subunit)
                    <option value="{{ $subunit->id }}"
                        {{ old('subunit_id', $jenisdataptj->subunit_id ?? '') == $subunit->id ? 'selected' : '' }}>
                        {{ $subunit->name }}
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
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Data</label>
                <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name"
                    name="name" value="{{ old('name') ?? ($jenisdataptj->name ?? '') }}">
                @if ($errors->has('name'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('name') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label for="publish_status" class="form-label">Status</label>
                <div class="form-check">
                    <input type="radio" id="aktif" name="publish_status" value="1"
                        {{ ($jenisdataptj->publish_status ?? '') == 'Aktif' ? 'checked' : '' }}>
                    <label class="form-check-label" for="aktif">Aktif</label>
                </div>
                <div class="form-check">
                    <input type="radio" id="tidak_aktif" name="publish_status" value="0"
                        {{ ($jenisdataptj->publish_status ?? '') == 'Tidak Aktif' ? 'checked' : '' }}>
                    <label class="form-check-label" for="tidak_aktif">Tidak Aktif</label>
                </div>
                @if ($errors->has('publish_status'))
                <div class="invalid-feedback">
                    @foreach ($errors->get('publish_status') as $error)
                    {{ $error }}
                    @endforeach
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ $str_mode }}</button>
        </form>
    </div>
</div>
<script>
$(document).ready(function() {
    function loadSubunits(departmentId, selectedSubunitId = null) {
        if (departmentId) {
            $.ajax({
                url: '{{ url("get-subunits") }}/' + departmentId,
                type: 'GET',
                success: function(data) {
                    $('#subunit_id').empty();

                    if (data.length === 0) {
                        $('#subunit_id').append('<option disabled selected>Tiada Sub Unit</option>');
                    } else {
                        $('#subunit_id').append('<option disabled selected>Pilih Sub Unit</option>');
                        $.each(data, function(index, subunit) {
                            $('#subunit_id').append('<option value="' + subunit.id + '"'
                                + (subunit.id == selectedSubunitId ? ' selected' : '')
                                + '>' + subunit.name + '</option>');
                        });
                    }
                }
            });
        }
    }

    // Bila department ditukar
    $('#department_id').on('change', function() {
        loadSubunits($(this).val());
    });

    // Auto-load masa page load kalau edit
    @if(isset($jenisdataptj))
        loadSubunits('{{ $jenisdataptj->department_id }}', '{{ $jenisdataptj->subunit_id }}');
    @endif
});
</script>

<!-- End Page Wrapper -->
@endsection