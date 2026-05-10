@extends('layouts.app')

@section('title', 'Edit Doctor')
@section('page-title', 'Edit Doctor')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Doctors', 'url' => route('admin.doctors.index')],
    ['label' => 'Dr. ' . $doctor->full_name],
    ['label' => 'Edit'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Doctor — Dr. {{ $doctor->full_name }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
            @csrf @method('PUT')

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $doctor->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $doctor->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select id="department_id" name="department_id" class="form-select">
                        <option value="">None</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}"
                            {{ old('department_id', $doctor->department_id) == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                    <input type="text" id="specialization" name="specialization"
                           class="form-control @error('specialization') is-invalid @enderror"
                           value="{{ old('specialization', $doctor->specialization) }}" required>
                    @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="qualification" class="form-label">Qualification</label>
                    <input type="text" id="qualification" name="qualification"
                           class="form-control"
                           value="{{ old('qualification', $doctor->qualification) }}">
                </div>
                <div class="col-md-4">
                    <label for="experience_years" class="form-label">Experience (years)</label>
                    <input type="number" id="experience_years" name="experience_years"
                           class="form-control"
                           value="{{ old('experience_years', $doctor->experience_years) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label for="consultation_fee" class="form-label">Consultation Fee <span class="text-danger">*</span></label>
                    <input type="number" id="consultation_fee" name="consultation_fee"
                           class="form-control @error('consultation_fee') is-invalid @enderror"
                           value="{{ old('consultation_fee', $doctor->consultation_fee) }}" min="0" step="0.01" required>
                    @error('consultation_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="phone_number" class="form-label">Phone</label>
                    <input type="text" id="phone_number" name="phone_number"
                           class="form-control"
                           value="{{ old('phone_number', $doctor->phone_number) }}">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" {{ old('status', $doctor->status->value) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $doctor->status->value) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status', $doctor->status->value) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="bio" class="form-label">Bio</label>
                    <textarea id="bio" name="bio" rows="3" class="form-control">{{ old('bio', $doctor->bio) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
