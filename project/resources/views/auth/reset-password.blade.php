@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<h4 class="mb-4 text-center fw-bold">Reset Password</h4>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $email ?? '') }}"
               required
               autofocus
               autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password"
               id="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               required
               autocomplete="new-password">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm New Password</label>
        <input type="password"
               id="password_confirmation"
               name="password_confirmation"
               class="form-control @error('password_confirmation') is-invalid @enderror"
               required
               autocomplete="new-password">
        @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
        <i class="fas fa-lock me-2"></i>Reset Password
    </button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i>Back to login
    </a>
</div>
@endsection
