@extends('layouts.guest')

@section('title', '404 Not Found')

@section('content')
<div class="text-center py-3">
    <div class="mb-3">
        <i class="fas fa-search fa-4x text-secondary"></i>
    </div>
    <h4 class="fw-bold mb-2">Page Not Found</h4>
    <p class="text-muted mb-4">
        The page you are looking for does not exist or may have been moved.
        Please check the address and try again.
    </p>
    @auth
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Back to Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </a>
    @endauth
</div>
@endsection
