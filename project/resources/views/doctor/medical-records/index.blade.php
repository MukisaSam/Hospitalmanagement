@extends('layouts.app')
@section('title', 'My Medical Records')
@section('content')
<x-breadcrumb :items="[['label' => 'My Medical Records']]" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-medical me-2 text-primary"></i>My Medical Records</h4>
    <a href="{{ route('doctor.medical-records.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i>New Record
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Visit Date</th>
                        <th>Patient</th>
                        <th>Diagnosis</th>
                        <th>Follow-up</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        <td class="fw-semibold">{{ $record->visit_date->format('d M Y') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $record->patient->full_name }}</div>
                            <div class="text-muted small">MRN: {{ $record->patient->mrn }}</div>
                            @if($record->appointment)
                                <div class="text-muted small">Appt: {{ $record->appointment->appointment_date->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($record->diagnosis, 60) }}</td>
                        <td>
                            @if($record->follow_up_date)
                                <span class="badge bg-info text-dark">{{ $record->follow_up_date->format('d M Y') }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('doctor.medical-records.show', $record) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('doctor.medical-records.edit', $record) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No medical records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($records->hasPages())
    <div class="card-footer bg-white">
        {!! $records->links() !!}
    </div>
    @endif
</div>
@endsection
