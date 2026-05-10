@props(['status'])

@php
$colors = [
    'pending'     => 'bg-secondary',
    'confirmed'   => 'bg-primary',
    'checked_in'  => 'bg-info text-dark',
    'in_progress' => 'bg-warning text-dark',
    'completed'   => 'bg-success',
    'cancelled'   => 'bg-danger',
    'no_show'     => 'bg-dark',
    'unpaid'      => 'bg-danger',
    'partial'     => 'bg-warning text-dark',
    'paid'        => 'bg-success',
    'active'      => 'bg-success',
    'inactive'    => 'bg-secondary',
    'suspended'   => 'bg-danger',
];

$color = $colors[$status] ?? 'bg-secondary';
$label = ucwords(str_replace('_', ' ', $status));
@endphp

<span class="badge {{ $color }}">{{ $label }}</span>
