@extends('layouts.app')

@section('content')
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center min-vh-100 py-2 mt-2">
            <div class="container">
                <div class="text-center">
                    <div class="d-flex align-items-center justify-content-center flex-column flex-md-row mb-4">
                        <img src="{{ asset('public/assets/images/putih.png') }}" class="logo-icon-login" alt="logo icon">
                        <div class="ms-3">
                            <h4 class="logo-text-login mb-0">WROOM</h4>
                            <h6 class="logo-subtitle-login mb-0">War Room 2.0</h6>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body p-3">
                                <div class="border p-3 rounded">
                                    <div class="text-center mb-2">
                                        <h3 class="">Daftar Akaun</h3>
                                    </div>
                                    <form method="POST" action="{{ route('register.store') }}">
                                        {{ csrf_field() }}

                                        <div class="row g-2">
                                            <div class="col-md-12">
                                                <label for="name" class="form-label">Nama Penuh</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    id="name" name="name" value="{{ old('name') }}" required>
                                                @if ($errors->has('name'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('name') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="staff_id" class="form-label">No. Pekerja</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('staff_id') ? 'is-invalid' : '' }}"
                                                    id="staff_id" name="staff_id" value="{{ old('staff_id') }}" required>
                                                @if ($errors->has('staff_id'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('staff_id') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Alamat Emel UiTM</label>
                                                <input type="email"
                                                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                    id="email" name="email" value="{{ old('email') }}" required>
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('email') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="office_phone_no" class="form-label">No. Telefon</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('office_phone_no') ? 'is-invalid' : '' }}"
                                                    id="office_phone_no" name="office_phone_no"
                                                    value="{{ old('office_phone_no') }}">
                                                @if ($errors->has('office_phone_no'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('office_phone_no') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="position_id" class="form-label">Jawatan</label>
                                                <select
                                                    class="form-select {{ $errors->has('position_id') ? 'is-invalid' : '' }}"
                                                    id="position_id" name="position_id" required>
                                                    <option value="" disabled selected>Pilih Jawatan</option>
                                                    @foreach ($positionList as $position)
                                                        <option value="{{ $position->id }}"
                                                            {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                                            {{ $position->title }} ({{ $position->grade }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('position_id'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('position_id') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="department_id" class="form-label">PTJ</label>
                                                <select
                                                    class="form-select {{ $errors->has('department_id') ? 'is-invalid' : '' }}"
                                                    id="department_id" name="department_id" required>
                                                    <option value="" disabled selected>Pilih PTJ</option>
                                                    @foreach ($departmentList as $department)
                                                        <option value="{{ $department->id }}"
                                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
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

                                            <div class="col-md-6">
                                                <label for="campus_id" class="form-label">Kampus</label>
                                                <select
                                                    class="form-select {{ $errors->has('campus_id') ? 'is-invalid' : '' }}"
                                                    id="campus_id" name="campus_id" required>
                                                    <option value="" disabled selected>Pilih Kampus</option>
                                                    @foreach ($campusList as $campus)
                                                        <option value="{{ $campus->id }}"
                                                            {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                                            {{ $campus->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('campus_id'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('campus_id') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="password" class="form-label">Kata Laluan</label>
                                                <input type="password"
                                                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                                    id="password" name="password" required>
                                                @if ($errors->has('password'))
                                                    <div class="invalid-feedback">
                                                        @foreach ($errors->get('password') as $error)
                                                            {{ $error }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label for="password-confirm" class="form-label">Sahkan Kata
                                                    Laluan</label>
                                                <input type="password" class="form-control" id="password-confirm"
                                                    name="password_confirmation" required>
                                            </div>

                                            <div class="col-12 d-grid mt-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bx bxs-user-plus"></i> Daftar Akaun
                                                </button>
                                            </div>

                                            <div class="col-12 text-center">
                                                <p class="mb-0 mt-2">Sudah ada akaun? <a href="{{ route('login') }}">Log
                                                        Masuk</a></p>
                                            </div>
                                        </div>
                                    </form>
                                </div><!-- card-body -->
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end wrapper-->
@endsection
