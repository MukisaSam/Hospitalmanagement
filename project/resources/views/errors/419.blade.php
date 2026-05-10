@extends('layouts.guest')

@section('title', '419 Session Expired')

@section('content')
<div class="text-center py-3">
    <div class="mb-3">
        <i class="fas fa-clock fa-4x text-warning"></i>
    </div>
    <h4 class="fw-bold mb-2">Session Expired</h4>
    <p class="text-muted mb-4">
        Your session has timed out for security reasons.
        Please go back and try again, or return to the login page.
    </p>
    <div class="d-flex gap-2 justify-content-center">
        <button onclick="history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Go Back
        </button>
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In Again
        </a>
    </div>
</div>
@endsection
