@extends('layouts.guest')

@section('title', '500 Server Error')

@section('content')
<div class="text-center py-3">
    <div class="mb-3">
        <i class="fas fa-exclamation-triangle fa-4x text-danger"></i>
    </div>
    <h4 class="fw-bold mb-2">Something Went Wrong</h4>
    <p class="text-muted mb-4">
        We encountered an unexpected error while processing your request.
        Our team has been notified. Please try again in a few moments.
    </p>
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Back to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Return to Login
        </a>
    @endauth
</div>
@endsection
