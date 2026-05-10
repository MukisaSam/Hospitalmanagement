@extends('layouts.app')

@section('title', 'Appointment Details')
@section('page-title', 'Appointment Details')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Appointments', 'url' => route('appointments.index')],
    ['label' => 'Details'],
]" />

@php $role = auth()->user()->role->value; @endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Appointment #{{ $appointment->id }}</h5>
                <x-status-badge :status="$appointment->status->value" />
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Patient</label>
                        <p class="fw-semibold mb-0">
                            <a href="{{ route('patients.show', $appointment->patient_id) }}">
                                {{ $appointment->patient->full_name ?? '—' }}
                            </a>
                        </p>
                        <p class="text-muted small">{{ $appointment->patient->mrn ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Doctor</label>
                        <p class="fw-semibold mb-0">Dr. {{ $appointment->doctor->full_name ?? '—' }}</p>
                        <p class="text-muted small">{{ $appointment->doctor->department->name ?? '' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Date</label>
                        <p class="mb-0">{{ $appointment->appointment_date?->format('D, d M Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Time</label>
                        <p class="mb-0">{{ substr($appointment->appointment_time, 0, 5) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Type</label>
                        <p class="mb-0"><span class="badge bg-light text-dark border">{{ strtoupper($appointment->type?->value ?? '') }}</span></p>
                    </div>
                    @if($appointment->notes)
                    <div class="col-12">
                        <label class="form-label text-muted small">Notes</label>
                        <p class="mb-0">{{ $appointment->notes }}</p>
                    </div>
                    @endif
                    @if($appointment->status->value === 'cancelled' && $appointment->cancellation_reason)
                    <div class="col-12">
                        <label class="form-label text-muted small">Cancellation Reason</label>
                        <p class="mb-0 text-danger">{{ $appointment->cancellation_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Status actions for admin/receptionist --}}
            @if(in_array($role, ['admin', 'receptionist']))
            <div class="card-footer bg-white">
                <div class="d-flex flex-wrap gap-2">
                    @php $status = $appointment->status->value; @endphp

                    @if(in_array($status, ['pending', 'confirmed']))
                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-calendar-alt me-1"></i> Reschedule
                    </a>
                    @endif

                    @foreach([
                        'pending' => ['confirmed' => 'Confirm', 'cancelled' => 'Cancel'],
                        'confirmed' => ['checked_in' => 'Check In', 'cancelled' => 'Cancel', 'no_show' => 'No Show'],
                        'checked_in' => ['in_progress' => 'Start Consultation', 'no_show' => 'No Show'],
                    ] as $fromStatus => $actions)
                        @if($status === $fromStatus)
                            @foreach($actions as $toStatus => $label)
                                @if($toStatus === 'cancelled')
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#cancelModal">
                                        <i class="fas fa-times me-1"></i> {{ $label }}
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('appointments.status', $appointment) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $toStatus }}">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            {{ $label }}
                                        </button>
                                    </form>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Status Timeline --}}
        @if($appointment->logs->isNotEmpty())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Status History</h6>
            </div>
            <div class="card-body">
                @foreach($appointment->logs as $log)
                <div class="d-flex gap-3 mb-3">
                    <div class="text-muted small" style="min-width:120px;">{{ $log->created_at?->format('d M Y H:i') }}</div>
                    <div>
                        <x-status-badge :status="$log->old_status" /> &rarr;
                        <x-status-badge :status="$log->new_status" />
                        @if($log->reason)
                        <p class="text-muted small mb-0 mt-1">{{ $log->reason }}</p>
                        @endif
                        <p class="text-muted small mb-0">by {{ $log->changedBy->name ?? 'System' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        {{-- Invoice --}}
        @if($appointment->invoice)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Invoice</h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><code>{{ $appointment->invoice->invoice_number }}</code></p>
                <p class="mb-1">Total: <strong>{{ number_format($appointment->invoice->total_amount, 2) }}</strong></p>
                <x-status-badge :status="$appointment->invoice->status->value" />
                <div class="mt-3">
                    <a href="{{ route('invoices.show', $appointment->invoice) }}" class="btn btn-sm btn-outline-primary">
                        View Invoice
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- Medical Record --}}
        @if($appointment->medicalRecord)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Medical Record</h6>
            </div>
            <div class="card-body">
                <p class="mb-1 small"><strong>Visit:</strong> {{ $appointment->medicalRecord->visit_date?->format('d M Y') }}</p>
                <p class="mb-1 small"><strong>Diagnosis:</strong> {{ $appointment->medicalRecord->diagnosis ?: 'Pending' }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                @csrf @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for Cancellation <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                                  class="form-control" placeholder="Please provide a reason (minimum 10 characters)..." required minlength="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
