@props([
    'status',
])

@php
    $map = [
        'pending'    => 'warning',
        'approved'   => 'success',
        'completed'  => 'success',
        'rejected'   => 'danger',
        'cancelled'  => 'danger',
        'accepted'   => 'info',
        'assigned'   => 'primary',
        'in_transit' => 'secondary',
        'delivered'  => 'success',
    ];

    $color = $map[strtolower($status)] ?? 'secondary';
@endphp

<span class="badge bg-{{ $color }}">{{ __(ucfirst(str_replace('_', ' ', $status))) }}</span>
