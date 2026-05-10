@extends('layouts.app')

@section('title', 'Archived Departments')
@section('page-title', 'Archived Departments')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Departments', 'url' => route('admin.departments.index')],
    ['label' => 'Archived'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between">
        <h5 class="mb-0">Archived Departments</h5>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Name</th><th>Archived At</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td>{{ $dept->name }}</td>
                    <td>{{ $dept->deleted_at?->format('d M Y') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.departments.restore', $dept->id) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-undo me-1"></i> Restore
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No archived departments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white">{{ $departments->links() }}</div>
</div>
@endsection
