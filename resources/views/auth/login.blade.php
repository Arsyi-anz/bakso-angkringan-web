{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.jpeg') }}" class="brand-logo mx-auto mb-3" alt="Bakso Angkringan">
            <h5 class="fw-bold mb-1">Admin Bakso Angkringan</h5>
            <p class="section-subtitle mb-0">Masuk menggunakan akun admin yang terdaftar</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@baksoangkringan.com" required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="********" required>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-brand w-100">Masuk</button>
        </form>
    </div>
</div>
@endsection
