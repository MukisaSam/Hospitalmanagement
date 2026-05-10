@extends('layouts.app')

@section('title', $patient->full_name)
@section('page-title', 'Patient Profile')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Patients', 'url' => route('patients.index')],
    ['label' => $patient->full_name],
]" />

<div class="row g-4">
    {{-- Left: Personal Details --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mx-auto mb-3"
                     style="width:80px;height:80px;">
                    <i class="fas fa-user fa-2x text-primary"></i>
                </div>
                <h5 class="mb-0">{{ $patient->full_name }}</h5>
                <p class="text-muted small mb-1"><code>{{ $patient->mrn }}</code></p>
                <span class="badge bg-secondary">{{ ucfirst($patient->gender?->value ?? 'Unknown') }}</span>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Personal Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Date of Birth</dt>
                    <dd class="col-7">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Blood Group</dt>
                    <dd class="col-7">{{ $patient->blood_group?->value ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Marital Status</dt>
                    <dd class="col-7">{{ ucfirst($patient->marital_status?->value ?? '—') }}</dd>
                    <dt class="col-5 text-muted">National ID</dt>
                    <dd class="col-7">{{ $patient->national_id ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Phone</dt>
                    <dd class="col-7">{{ $patient->phone_number }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $patient->email ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Address</dt>
                    <dd class="col-7">{{ $patient->address ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Allergies</dt>
                    <dd class="col-7">{{ $patient->allergies ?? 'None known' }}</dd>
                </dl>
            </div>
            <div class="card-footer bg-white">
                <h6 class="fw-semibold small mb-2">Emergency Contact</h6>
                <p class="mb-0 small">{{ $patient->emergency_contact_name ?? '—' }}</p>
                <p class="mb-0 small text-muted">{{ $patient->emergency_contact_phone ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- Right: Activity --}}
    <div class="col-lg-8">
        {{-- Actions --}}
        <div class="d-flex gap-2 mb-4">
            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
               class="btn btn-primary btn-sm">
                <i class="fas fa-calendar-plus me-1"></i> Book Appointment
            </a>
            <a href="{{ route('patients.edit', $patient) }}"
               class="btn btn-outline-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>

        {{-- Recent Appointments --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Recent Appointments</h6>
                <a href="{{ route('appointments.index', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($patient->appointments->isEmpty())
                    <p class="text-muted text-center py-3 mb-0">No appointments yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Date</th><th>Doctor</th><th>Type</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($patient->appointments as $appt)
                            <tr>
                                <td><a href="{{ route('appointments.show', $appt) }}">{{ $appt->appointment_date?->format('d M Y') }}</a></td>
                                <td>{{ $appt->doctor->full_name ?? '—' }}</td>
                                <td>{{ strtoupper($appt->type?->value ?? '—') }}</td>
                                <td><x-status-badge :status="$appt->status->value" /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Recent Medical Records --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Recent Medical Records</h6>
            </div>
            <div class="card-body p-0">
                @if($patient->medicalRecords->isEmpty())
                    <p class="text-muted text-center py-3 mb-0">No medical records yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Visit Date</th><th>Doctor</th><th>Diagnosis</th></tr>
                        </thead>
                        <tbody>
                            @foreach($patient->medicalRecords as $record)
                            <tr>
                                <td>{{ $record->visit_date?->format('d M Y') }}</td>
                                <td>{{ $record->doctor->full_name ?? '—' }}</td>
                                <td>{{ $record->diagnosis ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        {{-- Invoices --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Invoices</h6>
                <a href="{{ route('invoices.index', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($patient->invoices->isEmpty())
                    <p class="text-muted text-center py-3 mb-0">No invoices yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Invoice #</th><th>Total</th><th>Paid</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach($patient->invoices as $invoice)
                            <tr>
                                <td><code>{{ $invoice->invoice_number }}</code></td>
                                <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ number_format($invoice->amount_paid, 2) }}</td>
                                <td><x-status-badge :status="$invoice->status->value" /></td>
                                <td><a href="{{ route('invoices.show', $invoice) }}" class="btn btn-xs btn-sm btn-outline-primary">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
