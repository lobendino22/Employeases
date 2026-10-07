@extends('layouts.admin')

@section('title', 'Reports')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Reports']]" />
@endsection

@push('styles')
<style>
    :root {
        --rpt-navy: #0A2540;
        --rpt-navy-soft: #12345c;
        --rpt-gold: #E3A008;
        --rpt-gold-soft: #FDF3DC;
        --rpt-ink: #1F2933;
        --rpt-muted: #6B7280;
        --rpt-border: #E7EAEE;
        --rpt-radius: 14px;
    }

    .rpt-card {
        border: 1px solid var(--rpt-border);
        border-radius: var(--rpt-radius);
        background: #fff;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .rpt-card .card-header {
        border-radius: var(--rpt-radius) var(--rpt-radius) 0 0 !important;
    }

    /* Summary stat cards */
    .rpt-stat {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.4rem;
    }

    .rpt-stat-icon {
        flex: 0 0 auto;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .rpt-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--rpt-ink);
        line-height: 1.1;
    }

    .rpt-stat-label {
        color: var(--rpt-muted);
        font-size: 0.85rem;
    }

    .rpt-stat--applicants .rpt-stat-icon { background: #E9EEF5; color: var(--rpt-navy); }
    .rpt-stat--vacancies .rpt-stat-icon  { background: #E6F4EA; color: #1E7E42; }
    .rpt-stat--seekers .rpt-stat-icon    { background: var(--rpt-gold-soft); color: #A66A00; }

    /* Section headers */
    .rpt-section-title {
        font-weight: 600;
        color: var(--rpt-ink);
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin: 0;
    }

    .rpt-section-title .rpt-icon-chip {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #E9EEF5;
        color: var(--rpt-navy);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Tables */
    .rpt-table thead th {
        font-size: 0.72rem;
        letter-spacing: 0.02em;
        color: var(--rpt-muted);
        font-weight: 600;
        border-bottom: 1px solid var(--rpt-border);
        padding-bottom: 0.6rem;
    }

    .rpt-table td {
        border-color: var(--rpt-border);
        padding-top: 0.65rem;
        padding-bottom: 0.65rem;
    }

    .rpt-table tbody tr:hover {
        background-color: #F8FAFC;
    }

    .rpt-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
    }

    .rpt-empty {
        text-align: center;
        color: var(--rpt-muted);
        padding: 2rem 1rem;
        font-size: 0.9rem;
    }

    .rpt-chart-wrap {
        position: relative;
        min-height: 180px;
    }

    .rpt-card--chart .card-body {
        padding: 1rem 1.1rem;
    }

    @media (max-width: 767px) {
        .rpt-header-actions {
            width: 100%;
        }
        .rpt-header-actions .btn {
            flex: 1 1 auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color: var(--rpt-ink)">Reports &amp; Analytics</h4>
            <p class="text-muted mb-0">View employment statistics and generate reports</p>
        </div>
        <div class="d-flex flex-column flex-sm-row gap-2 rpt-header-actions">
            <a href="{{ route('admin.reports.export-pdf') }}" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>
                <span class="d-none d-sm-inline">Export </span>PDF
            </a>
            <a href="{{ route('admin.reports.export-excel') }}" class="btn" style="background: var(--rpt-navy); color: #fff;">
                <i class="bi bi-file-earmark-excel me-1"></i>
                <span class="d-none d-sm-inline">Export </span>Excel
            </a>
        </div>
    </div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="rpt-card rpt-stat rpt-stat--applicants h-100">
            <div class="rpt-stat-icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="rpt-stat-value">{{ number_format($totalApplicants) }}</div>
                <div class="rpt-stat-label">Total Applicants</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="rpt-card rpt-stat rpt-stat--vacancies h-100">
            <div class="rpt-stat-icon"><i class="bi bi-briefcase-fill"></i></div>
            <div>
                <div class="rpt-stat-value">{{ number_format($totalVacancies) }}</div>
                <div class="rpt-stat-label">Total Vacancies</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="rpt-card rpt-stat rpt-stat--seekers h-100">
            <div class="rpt-stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div>
                <div class="rpt-stat-value">{{ number_format($totalJobSeekers) }}</div>
                <div class="rpt-stat-label">Registered Job Seekers</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Applications by Status -->
    <div class="col-md-6">
        <div class="rpt-card rpt-card--chart h-100">
            <div class="card-header bg-white py-2 border-bottom">
                <h5 class="rpt-section-title" style="font-size: 0.95rem;">
                    <span class="rpt-icon-chip"><i class="bi bi-pie-chart"></i></span>
                    Applications by Status
                </h5>
            </div>
            <div class="card-body">
                @if($applicationsByStatus->isEmpty())
                    <p class="rpt-empty">No applications yet — data will appear here once candidates start applying.</p>
                @else
                    <div class="rpt-chart-wrap">
                        <div class="skeleton-chart" aria-hidden="true"></div>
                        <canvas id="statusChart" height="180"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Monthly Applications -->
    <div class="col-md-6">
        <div class="rpt-card rpt-card--chart h-100">
            <div class="card-header bg-white py-2 border-bottom">
                <h5 class="rpt-section-title" style="font-size: 0.95rem;">
                    <span class="rpt-icon-chip"><i class="bi bi-bar-chart"></i></span>
                    Monthly Applications ({{ date('Y') }})
                </h5>
            </div>
            <div class="card-body">
                @if($monthlyApplications->isEmpty())
                    <p class="rpt-empty">No applications recorded for {{ date('Y') }} yet.</p>
                @else
                    <div class="rpt-chart-wrap">
                        <div class="skeleton-chart" aria-hidden="true"></div>
                        <canvas id="monthlyChart" height="180"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Vacancies by Category -->
    <div class="col-md-6">
        <div class="rpt-card h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="rpt-section-title">
                    <span class="rpt-icon-chip"><i class="bi bi-bar-chart-steps"></i></span>
                    Vacancies by Category
                </h5>
            </div>
            <div class="card-body">
                @if($vacanciesByCategory->isEmpty())
                    <p class="rpt-empty">No vacancy categories to show yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm rpt-table mb-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vacanciesByCategory as $cat)
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        <td class="text-end fw-semibold">{{ $cat->job_vacancies_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="col-md-6">
        <div class="rpt-card h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="rpt-section-title">
                    <span class="rpt-icon-chip"><i class="bi bi-list-check"></i></span>
                    Application Status Breakdown
                </h5>
            </div>
            <div class="card-body">
                @if($applicationsByStatus->isEmpty())
                    <p class="rpt-empty">No status data to show yet.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm rpt-table mb-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applicationsByStatus as $stat)
                                    <tr>
                                        <td><x-status-badge :status="$stat->status" /></td>
                                        <td class="text-end fw-semibold">{{ $stat->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Applications Table -->
<div class="rpt-card">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="rpt-section-title">
            <span class="rpt-icon-chip"><i class="bi bi-clock-history"></i></span>
            Recent Applications
        </h5>
    </div>
    <div class="card-body p-0">
        @if($recentApplications->isEmpty())
            <p class="rpt-empty mb-0">No recent applications to display.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 rpt-table">
                    <thead>
                        <tr>
                            <th class="ps-3">Applicant</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th class="pe-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $avatarPalette = ['#0A2540', '#1E7E42', '#A66A00', '#6f42c1', '#0d6efd', '#B4232C'];
                        @endphp
                        @foreach($recentApplications as $app)
                            @php
                                $avatarColor = $avatarPalette[crc32($app->user->name) % count($avatarPalette)];
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rpt-avatar" style="background: {{ $avatarColor }}">
                                            {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $app->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $app->jobVacancy->title }}</td>
                                <td class="text-muted">{{ $app->created_at->format('M d, Y') }}</td>
                                <td class="pe-3"><span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/charts.js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;

    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const statuses = @json($applicationsByStatus->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s))));
        const counts = @json($applicationsByStatus->pluck('total'));
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statuses,
                datasets: [{
                    data: counts,
                    backgroundColor: ['#E3A008', '#0A2540', '#1E7E42', '#6f42c1', '#0d6efd', '#B4232C'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
            }
        });
    }

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const months = @json($monthlyApplications->pluck('month')->map(fn($m) => \Carbon\Carbon::create(null, $m)->format('M')));
        const totals = @json($monthlyApplications->pluck('total'));

        const gradient = monthlyCtx.getContext('2d').createLinearGradient(0, 0, 0, 180);
        gradient.addColorStop(0, 'rgba(10,37,64,0.18)');
        gradient.addColorStop(1, 'rgba(10,37,64,0)');

        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Applications',
                    data: totals,
                    borderColor: '#0A2540',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: '#0A2540',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#EEF1F4' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Hide chart skeletons once both charts have rendered.
    if (typeof chartReady === 'function') {
        chartReady();
    }
});
</script>
@endpush