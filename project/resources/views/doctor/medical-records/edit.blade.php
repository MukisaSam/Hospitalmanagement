@extends('layouts.app')
@section('title', 'Edit Medical Record')
@section('content')
<x-breadcrumb :items="[
    ['label' => 'My Appointments', 'url' => route('doctor.appointments.index')],
    ['label' => 'Medical Record', 'url' => route('doctor.medical-records.show', $medicalRecord)],
    ['label' => 'Edit'],
]" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i>Edit Medical Record</h4>
    <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body bg-light rounded">
        <div class="row">
            <div class="col-sm-4"><strong>Patient:</strong> {{ $medicalRecord->patient->full_name }}</div>
            <div class="col-sm-4"><strong>MRN:</strong> {{ $medicalRecord->patient->mrn }}</div>
            <div class="col-sm-4"><strong>Visit Date:</strong> {{ $medicalRecord->visit_date->format('d M Y') }}</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('doctor.medical-records.update', $medicalRecord) }}">
    @csrf @method('PUT')
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label for="chief_complaint" class="form-label">Chief Complaint <span class="text-danger">*</span></label>
                <textarea id="chief_complaint" name="chief_complaint" rows="2"
                          class="form-control @error('chief_complaint') is-invalid @enderror"
                          required>{{ old('chief_complaint', $medicalRecord->chief_complaint) }}</textarea>
                @error('chief_complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="symptoms" class="form-label">Symptoms</label>
                <textarea id="symptoms" name="symptoms" rows="2"
                          class="form-control @error('symptoms') is-invalid @enderror">{{ old('symptoms', $medicalRecord->symptoms) }}</textarea>
                @error('symptoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-md-9 mb-3">
                    <label for="diagnosis" class="form-label">Diagnosis <span class="text-danger">*</span></label>
                    <textarea id="diagnosis" name="diagnosis" rows="2"
                              class="form-control @error('diagnosis') is-invalid @enderror"
                              required>{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                    @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="diagnosis_code" class="form-label">ICD-10 Code</label>
                    <input type="text" id="diagnosis_code" name="diagnosis_code"
                           class="form-control @error('diagnosis_code') is-invalid @enderror"
                           value="{{ old('diagnosis_code', $medicalRecord->diagnosis_code) }}" maxlength="20">
                    @error('diagnosis_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="treatment_plan" class="form-label">Treatment Plan</label>
                <textarea id="treatment_plan" name="treatment_plan" rows="3"
                          class="form-control @error('treatment_plan') is-invalid @enderror">{{ old('treatment_plan', $medicalRecord->treatment_plan) }}</textarea>
                @error('treatment_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Private Clinical Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $medicalRecord->notes) }}</textarea>
                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="follow_up_date" class="form-label">Follow-up Date</label>
                <input type="text" id="follow_up_date" name="follow_up_date"
                       class="form-control flatpickr-date @error('follow_up_date') is-invalid @enderror"
                       value="{{ old('follow_up_date', $medicalRecord->follow_up_date?->toDateString()) }}"
                       placeholder="YYYY-MM-DD">
                @error('follow_up_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="card-footer bg-white">
            <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i>Save Changes
            </button>
            <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
        </div>
    </div>
</form>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d', minDate: 'today' });</script>
@endpush
@endsection
