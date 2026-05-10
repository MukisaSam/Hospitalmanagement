@extends('layouts.app')

@section('title', 'Edit Schedule')
@section('page-title', 'Edit Schedule')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Doctors', 'url' => route('admin.doctors.index')],
    ['label' => 'Dr. ' . $doctor->full_name, 'url' => route('admin.doctors.schedules.index', $doctor)],
    ['label' => 'Edit Schedule'],
]" />

<div class="card border-0 shadow-sm" style="max-width:500px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit — {{ ucfirst($schedule->day_of_week->value) }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.doctors.schedules.update', [$doctor, $schedule]) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Day</label>
                <input type="text" class="form-control bg-light"
                       value="{{ ucfirst($schedule->day_of_week->value) }}" readonly>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="text" id="start_time" name="start_time"
                           class="form-control flatpickr-time @error('start_time') is-invalid @enderror"
                           value="{{ old('start_time', substr($schedule->start_time, 0, 5)) }}" required>
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="text" id="end_time" name="end_time"
                           class="form-control flatpickr-time @error('end_time') is-invalid @enderror"
                           value="{{ old('end_time', substr($schedule->end_time, 0, 5)) }}" required>
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="max_appointments" class="form-label">Max Appointments <span class="text-danger">*</span></label>
                <input type="number" id="max_appointments" name="max_appointments"
                       class="form-control @error('max_appointments') is-invalid @enderror"
                       value="{{ old('max_appointments', $schedule->max_appointments) }}" min="1" max="100" required>
                @error('max_appointments')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="is_available" name="is_available" value="1"
                       class="form-check-input" {{ old('is_available', $schedule->is_available) ? 'checked' : '' }}>
                <label for="is_available" class="form-check-label">Available (accepting appointments)</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Changes</button>
                <a href="{{ route('admin.doctors.schedules.index', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-time', { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true });
</script>
@endpush
