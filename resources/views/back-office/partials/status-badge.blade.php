@php
$statusClasses = [
    'active'   => 'badge bg-success',
    'de-active' => 'badge bg-secondary',
];
$badgeClass = $statusClasses[$status] ?? 'badge bg-light';
@endphp

<span class="{{ $badgeClass }}">
    {{ ucfirst($status) }}
</span>
