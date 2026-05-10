@extends('layouts.app')

@section('title', 'Register Patient')
@section('page-title', 'Register Patient')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Patients', 'url' => route('patients.index')],
    ['label' => 'Register New Patient'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Patient Registration</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('patients.store') }}">
            @csrf

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Personal Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name') }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                    <input type="text" id="date_of_birth" name="date_of_birth"
                           class="form-control flatpickr-dob @error('date_of_birth') is-invalid @enderror"
                           value="{{ old('date_of_birth') }}" placeholder="YYYY-MM-DD" required>
                    @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select gender</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="marital_status" class="form-label">Marital Status</label>
                    <select id="marital_status" name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                        <option value="">Select status</option>
                        <option value="single" {{ old('marital_status') === 'single' ? 'selected' : '' }}>Single</option>
                        <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                        <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>Divorced</option>
                        <option value="widowed" {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                    </select>
                    @error('marital_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="blood_group" class="form-label">Blood Group</label>
                    <select id="blood_group" name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                        <option value="">Unknown</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                        <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                    @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Contact Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" id="phone_number" name="phone_number"
                           class="form-control @error('phone_number') is-invalid @enderror"
                           value="{{ old('phone_number') }}" required>
                    @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    <div class="form-text">An email creates a patient portal account.</div>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="national_id" class="form-label">National ID</label>
                    <input type="text" id="national_id" name="national_id"
                           class="form-control @error('national_id') is-invalid @enderror"
                           value="{{ old('national_id') }}">
                    @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" rows="2"
                              class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Medical Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label for="allergies" class="form-label">Known Allergies</label>
                    <textarea id="allergies" name="allergies" rows="2"
                              class="form-control @error('allergies') is-invalid @enderror"
                              placeholder="List any known allergies...">{{ old('allergies') }}</textarea>
                    @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Emergency Contact</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="emergency_contact_name" class="form-label">Contact Name</label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                           class="form-control @error('emergency_contact_name') is-invalid @enderror"
                           value="{{ old('emergency_contact_name') }}">
                    @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                           class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                           value="{{ old('emergency_contact_phone') }}">
                    @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Register Patient
                </button>
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-dob', { dateFormat: 'Y-m-d', maxDate: new Date(Date.now() - 86400000) });
</script>
@endpush
