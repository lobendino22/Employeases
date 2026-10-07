@props([
    'status',
    'label' => null,
    'icon' => null,
])

@php
    // Consistent color + icon mapping:
    // green = approved/healthy, amber = pending, blue = in-progress, red = rejected/critical.
    $map = [
        'pending'              => ['var(--ds-amber-bg)',  'var(--ds-amber-fg)',  'bi-clock'],
        'under_review'         => ['var(--ds-blue-bg)',   'var(--ds-blue-fg)',   'bi-search'],
        'shortlisted'          => ['var(--ds-blue-bg)',   'var(--ds-blue-fg)',   'bi-star'],
        'interview_scheduled'  => ['var(--ds-violet-bg)', 'var(--ds-violet-fg)', 'bi-calendar-event'],
        'accepted'             => ['var(--ds-green-bg)',  'var(--ds-green-fg)',  'bi-check-circle'],
        'rejected'             => ['var(--ds-red-bg)',    'var(--ds-red-fg)',    'bi-x-circle'],
        'scheduled'            => ['var(--ds-blue-bg)',   'var(--ds-blue-fg)',   'bi-calendar-check'],
        'completed'            => ['var(--ds-green-bg)',  'var(--ds-green-fg)',  'bi-check-circle'],
        'cancelled'            => ['var(--ds-red-bg)',    'var(--ds-red-fg)',    'bi-x-circle'],
        'rescheduled'          => ['var(--ds-amber-bg)',  'var(--ds-amber-fg)',  'bi-arrow-clockwise'],
        'active'               => ['var(--ds-green-bg)',  'var(--ds-green-fg)',  'bi-check-circle'],
        'inactive'             => ['var(--ds-red-bg)',    'var(--ds-red-fg)',    'bi-x-circle'],
        'open'                 => ['var(--ds-green-bg)',  'var(--ds-green-fg)',  'bi-unlock'],
        'closed'               => ['var(--ds-red-bg)',    'var(--ds-red-fg)',    'bi-lock'],
        'archived'             => ['var(--ds-slate-bg)',  'var(--ds-slate-fg)',  'bi-archive'],
    ];

    $key = strtolower(trim((string) $status));
    [$bg, $fg, $defaultIcon] = $map[$key] ?? ['var(--ds-slate-bg)', 'var(--ds-slate-fg)', 'bi-circle'];
    $displayLabel = $label ?? ucwords(str_replace('_', ' ', $key));
    $displayIcon = $icon ?? $defaultIcon;
@endphp

<span class="status-badge" style="background:{{ $bg }};color:{{ $fg }};">
    <i class="bi {{ $displayIcon }}" aria-hidden="true"></i>
    <span>{{ $displayLabel }}</span>
</span>
