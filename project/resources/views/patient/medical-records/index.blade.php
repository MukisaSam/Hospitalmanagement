@extends('layouts.app')
@section('title', 'My Medical Records')
@section('content')
<x-breadcrumb :items="[['label' => 'My Medical Records']]" />
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-medical me-2 text-success"></i>My Medical Records</h4>
</div>

@forelse($records as $record)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold">{{ $record->visit_date->format('d F Y') }}</span>
            <span class="text-muted ms-2">— Dr. {{ $record->doctor->full_name }}</span>
        </div>
        @if($record->follow_up_date)
            <span class="badge bg-info text-dark">
                <i class="fas fa-calendar me-1"></i>Follow-up: {{ $record->follow_up_date->format('d M Y') }}
            </span>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small mb-1">Diagnosis</h6>
                <p class="fw-semibold mb-1">{{ $record->diagnosis }}
                    @if($record->diagnosis_code)
                        <span class="badge bg-light text-dark">{{ $record->diagnosis_code }}</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase small mb-1">Treatment Plan</h6>
                <p class="mb-1">{{ $record->treatment_plan ?: '—' }}</p>
            </div>
        </div>

        @if($showNotesToPatient && $record->notes)
        <div class="mt-3">
            <h6 class="text-muted text-uppercase small mb-1">Clinical Notes</h6>
            <p class="mb-0">{{ $record->notes }}</p>
        </div>
        @endif

        @if($record->prescriptions->isNotEmpty())
        <hr>
        <h6 class="text-muted text-uppercase small mb-2">Prescriptions</h6>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr><th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th></tr>
                </thead>
                <tbody>
                    @foreach($record->prescriptions as $rx)
                    <tr>
                        <td class="fw-semibold">{{ $rx->medicine_name }}</td>
                        <td>{{ $rx->dosage }}</td>
                        <td>{{ $rx->frequency }}</td>
                        <td>{{ $rx->duration }}</td>
                        <td>{{ $rx->instructions ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($record->vitals)
        <hr>
        <h6 class="text-muted text-uppercase small mb-2">Vital Signs</h6>
        <div class="row g-2">
            @if($record->vitals->blood_pressure)
            <div class="col-auto"><span class="badge bg-light text-dark border"><i class="fas fa-heartbeat me-1 text-danger"></i>BP: {{ $record->vitals->blood_pressure }}</span></div>
            @endif
            @if($record->vitals->pulse_rate)
            <div class="col-auto"><span class="badge bg-light text-dark border"><i class="fas fa-heart me-1 text-danger"></i>Pulse: {{ $record->vitals->pulse_rate }} bpm</span></div>
            @endif
            @if($record->vitals->temperature)
            <div class="col-auto"><span class="badge bg-light text-dark border"><i class="fas fa-thermometer-half me-1 text-warning"></i>Temp: {{ $record->vitals->temperature }}°C</span></div>
            @endif
            @if($record->vitals->bmi)
            <div class="col-auto"><span class="badge bg-light text-dark border">BMI: {{ $record->vitals->bmi }}</span></div>
            @endif
        </div>
        @endif
    </div>
</div>
@empty
<div class="card shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="fas fa-file-medical fa-3x mb-3 opacity-50"></i>
        <p>No medical records found.</p>
    </div>
</div>
@endforelse

@if($records->hasPages())
<div class="mt-3">{!! $records->links() !!}</div>
@endif
@endsection
