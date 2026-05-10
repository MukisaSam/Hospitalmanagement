@extends('layouts.app')

@section('title', 'Archived Patients')
@section('page-title', 'Archived Patients')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Patients', 'url' => route('patients.index')],
    ['label' => 'Archived'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-archive me-2 text-secondary"></i>Archived Patients</h5>
        <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Active
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MRN</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Archived At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td><code>{{ $patient->mrn }}</code></td>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->phone_number }}</td>
                        <td>{{ $patient->deleted_at?->format('d M Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('patients.restore', $patient->id) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-undo me-1"></i> Restore
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No archived patients.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $patients->links() }}
    </div>
</div>
@endsection
