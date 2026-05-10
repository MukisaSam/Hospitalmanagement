@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page-title', 'Book Appointment')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Appointments', 'url' => route('appointments.index')],
    ['label' => 'Book New'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-calendar-plus me-2 text-primary"></i>Book Appointment</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('appointments.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient <span class="text-danger">*</span></label>
                    <select id="patient_id" name="patient_id"
                            class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}"
                            {{ (old('patient_id', request('patient_id')) == $patient->id) ? 'selected' : '' }}>
                            {{ $patient->full_name }} — {{ $patient->mrn }}
                        </option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="doctor_id" class="form-label">Doctor <span class="text-danger">*</span></label>
                    <select id="doctor_id" name="doctor_id"
                            class="form-select @error('doctor_id') is-invalid @enderror" required>
                        <option value="">Select doctor</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            Dr. {{ $doctor->full_name }} ({{ $doctor->department->name ?? 'No Dept.' }})
                        </option>
                        @endforeach
                    </select>
                    @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="appointment_date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="text" id="appointment_date" name="appointment_date"
                           class="form-control flatpickr-date @error('appointment_date') is-invalid @enderror"
                           value="{{ old('appointment_date') }}" placeholder="YYYY-MM-DD" required>
                    @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="appointment_time" class="form-label">Time <span class="text-danger">*</span></label>
                    <input type="text" id="appointment_time" name="appointment_time"
                           class="form-control flatpickr-time @error('appointment_time') is-invalid @enderror"
                           value="{{ old('appointment_time') }}" placeholder="HH:MM" required>
                    @error('appointment_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="type" class="form-label">Appointment Type <span class="text-danger">*</span></label>
                    <select id="type" name="type"
                            class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">Select type</option>
                        <option value="opd" {{ old('type') === 'opd' ? 'selected' : '' }}>OPD (Outpatient)</option>
                        <option value="ipd" {{ old('type') === 'ipd' ? 'selected' : '' }}>IPD (Inpatient)</option>
                        <option value="emergency" {{ old('type') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="follow_up" {{ old('type') === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Additional notes for the appointment...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-check me-1"></i> Book Appointment
                </button>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d', minDate: 'today' });
    flatpickr('.flatpickr-time', { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true });
</script>
@endpush
