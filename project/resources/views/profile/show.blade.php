@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Profile'],
]" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;">
                        <i class="fas fa-user fa-xl text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                        <span class="badge bg-secondary">{{ ucfirst($user->role->value) }}</span>
                    </div>
                </div>
            </div>

            <div class="card-body pt-4">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email"
                               id="email"
                               class="form-control"
                               value="{{ $user->email }}"
                               disabled>
                        <div class="form-text">Email address cannot be changed here.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text"
                               class="form-control"
                               value="{{ ucfirst($user->role->value) }}"
                               disabled>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Account Status</label>
                        <div>
                            <x-status-badge :status="$user->status->value" />
                        </div>
                    </div>

                    @if($user->last_login_at)
                        <div class="mb-4">
                            <label class="form-label text-muted small">Last Login</label>
                            <p class="mb-0 small text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $user->last_login_at->diffForHumans() }}
                                ({{ $user->last_login_at->format('d M Y, H:i') }})
                            </p>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
