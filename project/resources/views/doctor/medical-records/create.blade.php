@extends('layouts.app')
@section('title', 'New Medical Record')
@section('content')
<x-breadcrumb :items="[
    ['label' => 'My Appointments', 'url' => route('doctor.appointments.index')],
    ['label' => 'New Medical Record'],
]" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-medical me-2 text-success"></i>Create Medical Record</h4>
</div>

<form method="POST" action="{{ route('doctor.medical-records.store') }}">
    @csrf
    @if($appointment)
    <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
    <input type="hidden" name="patient_id" value="{{ $appointment->patient_id }}">
    <div class="card shadow-sm mb-3">
        <div class="card-body bg-light">
            <div class="row">
                <div class="col-sm-4"><strong>Patient:</strong> {{ $appointment->patient->full_name }}</div>
                <div class="col-sm-4"><strong>MRN:</strong> {{ $appointment->patient->mrn }}</div>
                <div class="col-sm-4"><strong>Appointment:</strong> {{ $appointment->appointment_date->format('d M Y') }}</div>
            </div>
        </div>
    </div>
    @else
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                <select id="patient_id" name="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                    <option value="">Select Patient</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" @selected(old('patient_id') == $p->id)>{{ $p->full_name }} ({{ $p->mrn }})</option>
                    @endforeach
                </select>
                @error('patient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                    <input type="text" id="visit_date" name="visit_date"
                           class="form-control flatpickr-date @error('visit_date') is-invalid @enderror"
                           value="{{ old('visit_date', today()->toDateString()) }}" required>
                    @error('visit_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="chief_complaint" class="form-label">Chief Complaint <span class="text-danger">*</span></label>
                <textarea id="chief_complaint" name="chief_complaint" rows="2"
                          class="form-control @error('chief_complaint') is-invalid @enderror"
                          required>{{ old('chief_complaint') }}</textarea>
                @error('chief_complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="symptoms" class="form-label">Symptoms</label>
                <textarea id="symptoms" name="symptoms" rows="2" class="form-control">{{ old('symptoms') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-9 mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                    <textarea id="diagnosis" name="diagnosis" rows="2"
                              class="form-control @error('diagnosis') is-invalid @enderror"
                              required>{{ old('diagnosis') }}</textarea>
                    @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="diagnosis_code" class="form-label">ICD-10 Code</label>
                    <input type="text" id="diagnosis_code" name="diagnosis_code" class="form-control"
                           value="{{ old('diagnosis_code') }}" maxlength="20">
                </div>
            </div>
            <div class="mb-3">
                <label for="treatment_plan" class="form-label">Treatment Plan</label>
                <textarea id="treatment_plan" name="treatment_plan" rows="3" class="form-control">{{ old('treatment_plan') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Private Clinical Notes</label>
                <textarea id="notes" name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
            </div>
            <div class="col-md-4 mb-3">
                <label for="follow_up_date" class="form-label">Follow-up Date</label>
                <input type="text" id="follow_up_date" name="follow_up_date"
                       class="form-control flatpickr-date"
                       value="{{ old('follow_up_date') }}" placeholder="YYYY-MM-DD">
            </div>
        </div>
        <div class="card-footer bg-white">
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save me-1"></i>Create Record
            </button>
            <a href="{{ route('doctor.appointments.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </div>
</form>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });</script>
@endpush
@endsection
