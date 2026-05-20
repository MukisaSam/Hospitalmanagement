@extends('layouts.app')

@section('title', 'Add Doctor')
@section('page-title', 'Add Doctor')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Doctors', 'url' => route('admin.doctors.index')],
    ['label' => 'Add Doctor'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user-md me-2 text-primary"></i>Add Doctor</h5>
        <small class="text-muted">A user account will be created automatically with the default password "password".</small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.doctors.store') }}">
            @csrf

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Personal Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="email" class="form-label">Email (Login) <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Professional Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">None</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                    <input type="text" id="specialization" name="specialization"
                           class="form-control @error('specialization') is-invalid @enderror"
                           value="{{ old('specialization') }}" required>
                    @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="qualification" class="form-label">Qualification <span class="text-danger">*</span></label>
                    <input type="text" id="qualification" name="qualification"
                           class="form-control @error('qualification') is-invalid @enderror"
                           value="{{ old('qualification') }}" required>
                    @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="experience_years" class="form-label">Experience (years) <span class="text-danger">*</span></label>
                    <input type="number" id="experience_years" name="experience_years"
                           class="form-control @error('experience_years') is-invalid @enderror"
                           value="{{ old('experience_years') }}" min="0" required>
                    @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="consultation_fee" class="form-label">Consultation Fee <span class="text-danger">*</span></label>
                    <input type="number" id="consultation_fee" name="consultation_fee"
                           class="form-control @error('consultation_fee') is-invalid @enderror"
                           value="{{ old('consultation_fee', 0) }}" min="0" step="0.01" required>
                    @error('consultation_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="phone_number" class="form-label">Phone</label>
                    <input type="text" id="phone_number" name="phone_number"
                           class="form-control @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number') }}">
                    @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea id="bio" name="bio" rows="3"
                              class="form-control @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                    @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Add Doctor</button>
                <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
