@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<x-breadcrumb :items="[['label' => 'Audit Logs']]" />

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2">
            <div class="col-md-2">
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach(['created','updated','deleted','restored'] as $a)
                    <option value="{{ $a }}" {{ request('action') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="model_type" class="form-control" placeholder="Model (e.g. Patient)"
                       value="{{ request('model_type') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="from" class="form-control flatpickr-date" placeholder="From"
                       value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="to" class="form-control flatpickr-date" placeholder="To"
                       value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Model</th>
                        <th>Model ID</th>
                        <th>IP Address</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="small">{{ $log->created_at?->format('d M Y H:i') }}</td>
                        <td class="small">{{ $log->user->name ?? 'System' }}</td>
                        <td>
                            <span class="badge {{ $log->action->value === 'created' ? 'bg-success' : ($log->action->value === 'deleted' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                {{ ucfirst($log->action->value) }}
                            </span>
                        </td>
                        <td class="small">{{ class_basename($log->model_type) }}</td>
                        <td class="small">{{ $log->model_id }}</td>
                        <td class="small text-muted">{{ $log->ip_address }}</td>
                        <td>
                            <button type="button" class="btn btn-xs btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#log-modal-{{ $log->id }}">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    {{-- Detail modal --}}
                    <div class="modal fade" id="log-modal-{{ $log->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Audit Log #{{ $log->id }}</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p class="fw-semibold mb-1">Old Values</p>
                                            <pre class="bg-light p-2 rounded small" style="max-height:200px;overflow:auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="fw-semibold mb-1">New Values</p>
                                            <pre class="bg-light p-2 rounded small" style="max-height:200px;overflow:auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $logs->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
</script>
@endpush
