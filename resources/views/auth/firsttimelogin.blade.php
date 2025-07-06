@extends('layouts.app')

@section('content')
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
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
                    <div class="col-md-6 col-lg-5">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <div class="border p-4 rounded">
                                    <div class="text-center mb-4">
                                        <h3 class="">Pengesahan Akaun</h3>
                                        <p class="text-muted">Masukkan emel UiTM anda untuk menerima pautan set kata laluan
                                        </p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('firsttimelogin.send') }}">
                                        {{ csrf_field() }}
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Emel UiTM</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                required value="{{ old('email') }}">
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Hantar Pautan</button>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <a href="{{ route('login') }}">Kembali ke Log Masuk</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
