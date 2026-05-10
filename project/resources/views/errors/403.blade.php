@extends('layouts.guest')

@section('title', '403 Forbidden')

@section('content')
<div class="text-center py-3">
    <div class="mb-3">
        <i class="fas fa-ban fa-4x text-danger"></i>
    </div>
    <h4 class="fw-bold mb-2">Access Denied</h4>
    <p class="text-muted mb-4">
        You do not have permission to view this page.
        If you believe this is a mistake, please contact your administrator.
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
