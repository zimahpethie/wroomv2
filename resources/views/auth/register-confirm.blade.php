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
                <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 justify-content-center">
                    <div class="col mx-auto">
                        <div class="card shadow-none">
                            <div class="card-body">
                                <div class="border p-2 rounded">
                                    <div class="text-center mb-4">
                                        <h3>{{ __('Pendaftaran Berjaya') }}</h3>
                                    </div>

                                    <div class="text-center mb-3">
                                        <p class="mb-2">
                                            {{ __('Sila semak emel anda untuk pautan pengesahan sebelum log masuk.') }}
                                        </p>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
