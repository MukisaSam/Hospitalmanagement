@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<h4 class="mb-2 text-center fw-bold">Forgot Password</h4>
<p class="text-muted small text-center mb-4">
    Enter your email address and we will send you a link to reset your password.
</p>

@if(session('status'))
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}"
               required
               autofocus
               autocomplete="email">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-100">
        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
    </button>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i>Back to login
    </a>
</div>
@endsection
