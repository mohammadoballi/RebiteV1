@props([
    'status',
])

@php
    $map = [
        'pending'    => 'warning',
        'in_progress'=> 'info',
        'approved'   => 'success',
        'completed'  => 'success',
        'rejected'   => 'danger',
        'cancelled'  => 'danger',
    ];

    $color = $map[strtolower($status)] ?? 'secondary';
@endphp

<span class="badge bg-{{ $color }}">{{ __(ucfirst(str_replace('_', ' ', $status))) }}</span>
