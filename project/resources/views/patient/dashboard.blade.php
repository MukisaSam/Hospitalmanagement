@extends('layouts.app')
@section('title', 'My Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Welcome, {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0">{{ now()->format('l, d F Y') }}</p>
    </div>
</div>

<div class="row">
    {{-- Upcoming Appointments --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-calendar-alt me-1 text-primary"></i>Upcoming Appointments</span>
                <a href="{{ route('patient.appointments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($upcomingAppointments->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                        <p>No upcoming appointments.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th><th>Time</th><th>Doctor</th><th>Department</th><th>Type</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingAppointments as $appt)
                            <tr>
                                <td>{{ $appt->appointment_date->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('H:i') }}</td>
                                <td>Dr. {{ $appt->doctor->full_name }}</td>
                                <td>{{ $appt->doctor->department?->name ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($appt->type->value) }}</span></td>
                                <td><x-status-badge :status="$appt->status->value" /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Outstanding Invoices --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-file-invoice-dollar me-1 text-danger"></i>Outstanding Bills</span>
                <a href="{{ route('patient.invoices.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="card-body p-0">
                @if($outstandingInvoices->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-75"></i>
                        <p class="mb-0">No outstanding bills.</p>
                    </div>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($outstandingInvoices as $inv)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold small">{{ $inv->invoice_number }}</div>
                                <div class="text-muted small">Due: {{ $inv->due_date?->format('d M Y') ?? '—' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-danger">{{ number_format($inv->total_amount - $inv->amount_paid, 2) }}</div>
                                <x-status-badge :status="$inv->status->value" />
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent Medical Records --}}
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold"><i class="fas fa-file-medical me-1 text-success"></i>Recent Medical Records</span>
        <a href="{{ route('patient.medical-records.index') }}" class="btn btn-sm btn-outline-success">View All</a>
    </div>
    <div class="card-body p-0">
        @if($recentRecords->isEmpty())
            <div class="text-center text-muted py-4">
                <p>No medical records found.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Visit Date</th><th>Doctor</th><th>Diagnosis</th><th>Follow-up</th></tr>
                </thead>
                <tbody>
                    @foreach($recentRecords as $record)
                    <tr>
                        <td>{{ $record->visit_date->format('d M Y') }}</td>
                        <td>Dr. {{ $record->doctor->full_name }}</td>
                        <td>{{ Str::limit($record->diagnosis, 60) }}</td>
                        <td>{{ $record->follow_up_date?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
