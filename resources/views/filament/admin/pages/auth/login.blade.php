@extends('filament-panels::auth.login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pamat-login.css') }}">
@endpush

@section('content')
    <div class="pamat-login-wrapper">
        <div class="pamat-login-card">

            <div class="pamat-login-logo">
                <img src="{{ asset('images/logo.png') }}" alt="PaMat Logo">
            </div>

            <h1 class="pamat-login-title">Panel administratora</h1>

            {{ $this->form }}

            <button type="submit" form="login" class="pamat-login-button">
                Zaloguj się
            </button>

        </div>
    </div>
@endsection
