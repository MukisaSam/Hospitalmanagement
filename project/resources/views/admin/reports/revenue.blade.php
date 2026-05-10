@extends('layouts.app')

@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')

@section('content')
<x-breadcrumb :items="[['label' => 'Revenue Report']]" />

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.revenue') }}" class="row g-2">
            <div class="col-md-3">
                <input type="number" name="year" class="form-control" placeholder="Year"
                       value="{{ request('year', now()->year) }}" min="2020" max="{{ now()->year }}">
            </div>
            <div class="col-md-3">
                <select name="month" class="form-select">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.reports.revenue.export', request()->all()) }}"
                   class="btn btn-outline-success w-100">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                <p class="text-muted small mb-1">Total Revenue ({{ $year }})</p>
                <h3 class="fw-bold text-success">{{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">Monthly Breakdown</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Month</th><th>Year</th><th class="text-end">Revenue</th></tr>
            </thead>
            <tbody>
                @forelse($revenue as $row)
                <tr>
                    <td>{{ date('F', mktime(0,0,0,$row->month,1)) }}</td>
                    <td>{{ $row->year }}</td>
                    <td class="text-end">{{ number_format($row->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No revenue data for selected period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
