@extends('layouts.app')
@section('title', 'Medical Record')
@section('content')
<x-breadcrumb :items="[
    ['label' => 'My Appointments', 'url' => route('doctor.appointments.index')],
    ['label' => 'Medical Record'],
]" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-medical me-2 text-primary"></i>Medical Record</h4>
    <a href="{{ route('doctor.medical-records.edit', $medicalRecord) }}" class="btn btn-primary">
        <i class="fas fa-edit me-1"></i>Edit Record
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        {{-- Patient Info --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Patient Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6"><strong>Name:</strong> {{ $medicalRecord->patient->full_name }}</div>
                    <div class="col-sm-6"><strong>MRN:</strong> {{ $medicalRecord->patient->mrn }}</div>
                    <div class="col-sm-6 mt-2"><strong>Visit Date:</strong> {{ $medicalRecord->visit_date->format('d M Y') }}</div>
                    <div class="col-sm-6 mt-2"><strong>Doctor:</strong> Dr. {{ $medicalRecord->doctor->full_name }}</div>
                </div>
            </div>
        </div>

        {{-- Clinical Notes --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">Clinical Notes</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase">Chief Complaint</label>
                    <p>{{ $medicalRecord->chief_complaint ?: '—' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase">Symptoms</label>
                    <p>{{ $medicalRecord->symptoms ?: '—' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase">Diagnosis</label>
                    <p class="fw-semibold">{{ $medicalRecord->diagnosis ?: '—' }}
                        @if($medicalRecord->diagnosis_code)
                            <span class="badge bg-light text-dark ms-1">ICD-10: {{ $medicalRecord->diagnosis_code }}</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase">Treatment Plan</label>
                    <p>{{ $medicalRecord->treatment_plan ?: '—' }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted small text-uppercase">Private Notes</label>
                    <p>{{ $medicalRecord->notes ?: '—' }}</p>
                </div>
                @if($medicalRecord->follow_up_date)
                <div class="alert alert-info mb-0">
                    <i class="fas fa-calendar me-1"></i>Follow-up date: <strong>{{ $medicalRecord->follow_up_date->format('d M Y') }}</strong>
                </div>
                @endif
            </div>
        </div>

        {{-- Prescriptions --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Prescriptions</span>
                <a href="{{ route('doctor.medical-records.prescriptions.create', $medicalRecord) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus me-1"></i>Add Prescription
                </a>
            </div>
            <div class="card-body p-0">
                @if($medicalRecord->prescriptions->isEmpty())
                    <p class="text-center text-muted py-3">No prescriptions recorded.</p>
                @else
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medicine</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Instructions</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicalRecord->prescriptions as $rx)
                            <tr>
                                <td class="fw-semibold">{{ $rx->medicine_name }}</td>
                                <td>{{ $rx->dosage }}</td>
                                <td>{{ $rx->frequency }}</td>
                                <td>{{ $rx->duration }}</td>
                                <td>{{ $rx->instructions ?: '—' }}</td>
                                <td>
                                    <a href="{{ route('doctor.medical-records.prescriptions.edit', [$medicalRecord, $rx]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('doctor.medical-records.prescriptions.destroy', [$medicalRecord, $rx]) }}" class="d-inline" id="del-rx-{{ $rx->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button onclick="confirmDelete({{ $rx->id }}, 'rx', 'prescription')" class="btn btn-sm btn-outline-danger">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Vitals --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Vital Signs</span>
                @if(!$medicalRecord->vitals)
                    <a href="#" data-bs-toggle="modal" data-bs-target="#vitalsModal" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i>Record Vitals
                    </a>
                @else
                    <a href="#" data-bs-toggle="modal" data-bs-target="#vitalsModal" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endif
            </div>
            <div class="card-body">
                @if($medicalRecord->vitals)
                @php $v = $medicalRecord->vitals; @endphp
                <table class="table table-sm mb-0">
                    <tr><td class="text-muted">Blood Pressure</td><td>{{ $v->blood_pressure ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Pulse Rate</td><td>{{ $v->pulse_rate ? $v->pulse_rate . ' bpm' : '—' }}</td></tr>
                    <tr><td class="text-muted">Temperature</td><td>{{ $v->temperature ? $v->temperature . '°C' : '—' }}</td></tr>
                    <tr><td class="text-muted">Weight</td><td>{{ $v->weight ? $v->weight . ' kg' : '—' }}</td></tr>
                    <tr><td class="text-muted">Height</td><td>{{ $v->height ? $v->height . ' cm' : '—' }}</td></tr>
                    <tr><td class="text-muted">BMI</td><td>{{ $v->bmi ?? '—' }}</td></tr>
                    <tr><td class="text-muted">SpO₂</td><td>{{ $v->oxygen_saturation ? $v->oxygen_saturation . '%' : '—' }}</td></tr>
                </table>
                @else
                <p class="text-center text-muted py-2">No vitals recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Vitals Modal --}}
<div class="modal fade" id="vitalsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $medicalRecord->vitals ? 'Update' : 'Record' }} Vital Signs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @if($medicalRecord->vitals)
            <form method="POST" action="{{ route('doctor.medical-records.vitals.update', [$medicalRecord, $medicalRecord->vitals]) }}">
                @csrf @method('PUT')
            @else
            <form method="POST" action="{{ route('doctor.medical-records.vitals.store', $medicalRecord) }}">
                @csrf
            @endif
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Blood Pressure</label>
                        <input type="text" name="blood_pressure" class="form-control" placeholder="120/80 mmHg" value="{{ $medicalRecord->vitals?->blood_pressure }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" class="form-control" value="{{ $medicalRecord->vitals?->pulse_rate }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Temperature (°C)</label>
                        <input type="number" name="temperature" step="0.1" class="form-control" value="{{ $medicalRecord->vitals?->temperature }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">SpO₂ (%)</label>
                        <input type="number" name="oxygen_saturation" step="0.1" class="form-control" value="{{ $medicalRecord->vitals?->oxygen_saturation }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" name="weight" step="0.01" class="form-control" id="weight" value="{{ $medicalRecord->vitals?->weight }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" name="height" step="0.01" class="form-control" id="height" value="{{ $medicalRecord->vitals?->height }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Vitals</button>
            </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
function confirmDelete(id, prefix, type) {
    Swal.fire({
        title: 'Delete ' + type + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-' + prefix + '-' + id).submit(); });
}
</script>
@endpush
@endsection
