@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Patients', 'url' => route('patients.index')],
    ['label' => $patient->full_name, 'url' => route('patients.show', $patient)],
    ['label' => 'Edit'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Patient — {{ $patient->full_name }}</h5>
        <small class="text-muted">MRN: <code>{{ $patient->mrn }}</code></small>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('patients.update', $patient) }}">
            @csrf @method('PUT')

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Personal Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control @error('first_name') is-invalid @enderror"
                           value="{{ old('first_name', $patient->first_name) }}" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control @error('last_name') is-invalid @enderror"
                           value="{{ old('last_name', $patient->last_name) }}" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="text" class="form-control bg-light" value="{{ $patient->date_of_birth?->format('d M Y') }}" readonly>
                    <div class="form-text text-muted"><i class="fas fa-lock me-1"></i>Date of birth is immutable.</div>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="male" {{ old('gender', $patient->gender?->value) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient->gender?->value) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient->gender?->value) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="marital_status" class="form-label">Marital Status</label>
                    <select id="marital_status" name="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                        <option value="">Select status</option>
                        @foreach(['single','married','divorced','widowed'] as $ms)
                        <option value="{{ $ms }}" {{ old('marital_status', $patient->marital_status?->value) === $ms ? 'selected' : '' }}>
                            {{ ucfirst($ms) }}
                        </option>
                        @endforeach
                    </select>
                    @error('marital_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="blood_group" class="form-label">Blood Group</label>
                    <select id="blood_group" name="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                        <option value="">Unknown</option>
                        @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                        <option value="{{ $bg }}" {{ old('blood_group', $patient->blood_group?->value) === $bg ? 'selected' : '' }}>{{ $bg }}</option>
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
                           value="{{ old('phone_number', $patient->phone_number) }}" required>
                    @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $patient->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="national_id" class="form-label">National ID</label>
                    <input type="text" id="national_id" name="national_id"
                           class="form-control @error('national_id') is-invalid @enderror"
                           value="{{ old('national_id', $patient->national_id) }}">
                    @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="address" class="form-label">Address</label>
                    <textarea id="address" name="address" rows="2"
                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $patient->address) }}</textarea>
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Medical Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label for="allergies" class="form-label">Known Allergies</label>
                    <textarea id="allergies" name="allergies" rows="2"
                              class="form-control @error('allergies') is-invalid @enderror">{{ old('allergies', $patient->allergies) }}</textarea>
                    @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <h6 class="fw-semibold text-muted mb-3 border-bottom pb-2">Emergency Contact</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="emergency_contact_name" class="form-label">Contact Name</label>
                    <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                           class="form-control @error('emergency_contact_name') is-invalid @enderror"
                           value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}">
                    @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                    <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"
                           class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                           value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}">
                    @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
