@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Dashboard']]" />
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold">Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark p-2 fw-medium">
                <i class="bi bi-calendar3 me-1"></i> {{ now()->format('F d, Y') }}
            </span>
            <span class="badge bg-primary-subtle text-primary-emphasis p-2 fw-medium">
                <i class="bi bi-clock me-1"></i> {{ now()->format('g:i A') }}
            </span>
        </div>
    </div>
<!-- Primary Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm h-100 stat-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 stat-label small fw-medium">Total Vacancies</p>
                        <h4 class="fw-bold mb-0 text-primary">{{ $totalVacancies }}</h4>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-briefcase fs-5"></i>
                    </div>
                </div>
                <small class="text-muted stat-desc">All posted job vacancies</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm h-100 stat-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 stat-label small fw-medium">Total Applicants</p>
                        <h4 class="fw-bold mb-0 text-info">{{ $totalApplicants }}</h4>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <small class="text-muted stat-desc">All submitted applications</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm h-100 stat-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 stat-label small fw-medium">Active Jobs</p>
                        <h4 class="fw-bold mb-0 text-success">{{ $activeJobs }}</h4>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                </div>
                <small class="text-muted stat-desc">Currently accepting applicants</small>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm h-100 stat-card">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <p class="text-muted mb-1 stat-label small fw-medium">Closed Jobs</p>
                        <h4 class="fw-bold mb-0 text-danger">{{ $closedJobs }}</h4>
                    </div>
                    <div class="stat-icon bg-danger-subtle text-danger rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-x-circle fs-5"></i>
                    </div>
                </div>
                <small class="text-muted stat-desc">No longer accepting applicants</small>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm bg-brand-navy-deep text-white h-100 stat-card-colored">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-large bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-people-fill fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1 text-white">{{ $totalJobSeekers }}</h4>
                        <p class="mb-0 small text-white-50">Registered Job Seekers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm bg-brand-gold text-dark h-100 stat-card-colored">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-large bg-white bg-opacity-20 rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-clock-fill fs-4 text-dark opacity-75"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1">{{ $pendingApplications }}</h4>
                        <p class="mb-0 small opacity-75">Pending Applications</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm bg-brand-teal text-white h-100 stat-card-colored">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-large bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-calendar-check-fill fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1 text-white">{{ $interviewsScheduled }}</h4>
                        <p class="mb-0 small text-white-50">Upcoming Interviews</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="card border-0 shadow-sm bg-brand-navy text-white h-100 stat-card-colored">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon-large bg-white bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                        <i class="bi bi-bar-chart-fill fs-4 text-white opacity-75"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-1 text-white">{{ $totalApplicants > 0 ? round(($totalApplicants / max($totalVacancies, 1)) * 100) : 0 }}%</h4>
                        <p class="mb-0 small text-white-50">Application Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-bar-chart-line me-2 text-primary"></i>Applications per Month ({{ date('Y') }})
                    </h5>
                    <span class="badge bg-primary-subtle text-primary-emphasis small">
                        Total: {{ $totalApplicants }} applications
                    </span>
                </div>
            </div>
            <div class="card-body p-3">
                <div class="skeleton-chart" aria-hidden="true"></div>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="applicationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>Jobs by Category
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="skeleton-chart" aria-hidden="true"></div>
                <div class="chart-container" style="position: relative; height: 300px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Applications -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Applications
                    </h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-info-subtle text-info-emphasis small">
                            {{ $recentApplications->count() }} recent
                        </span>
                        <a href="{{ route('admin.applicants.index') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i>
                            <span class="d-none d-sm-inline">View </span>All
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($recentApplications->count() > 0)
                    <!-- Desktop Table View -->
                    <div class="d-none d-md-block">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold ps-3">Applicant</th>
                                        <th class="border-0 fw-semibold">Position</th>
                                        <th class="border-0 fw-semibold">Date</th>
                                        <th class="border-0 fw-semibold">Status</th>
                                        <th class="border-0 fw-semibold pe-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $app)
                                        <tr class="border-bottom border-opacity-50">
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;font-size:14px">
                                                        {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="mb-0 fw-medium text-truncate">{{ $app->user->name }}</p>
                                                        <small class="text-muted text-truncate d-block">{{ $app->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <a href="{{ route('admin.job-vacancies.show', $app->jobVacancy) }}" class="text-decoration-none fw-medium">
                                                    {{ Str::limit($app->jobVacancy->title, 25) }}
                                                </a>
                                            </td>
                                            <td class="py-3">
                                                <span class="text-muted small">{{ $app->created_at->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $app->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td class="py-3">
                                                <x-status-badge :status="$app->status" :label="$app->status_label" />
                                            </td>
                                            <td class="pe-3 py-3">
                                                <a href="{{ route('admin.applicants.show', $app) }}" class="btn btn-sm btn-outline-primary" title="View Application">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="d-md-none">
                        @foreach($recentApplications as $app)
                            <div class="border-bottom p-3">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;font-size:16px">
                                        {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="mb-1 fw-semibold text-truncate">{{ $app->user->name }}</h6>
                                        <p class="mb-2 text-muted small text-truncate">{{ $app->user->email }}</p>
                                        <div class="mb-2">
                                            <x-status-badge :status="$app->status" :label="$app->status_label" />
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="row g-2">
                                        <div class="col-8">
                                            <span class="text-muted d-block small">Position Applied</span>
                                            <a href="{{ route('admin.job-vacancies.show', $app->jobVacancy) }}" class="text-decoration-none fw-medium small">
                                                {{ $app->jobVacancy->title }}
                                            </a>
                                        </div>
                                        <div class="col-4 text-end">
                                            <span class="text-muted d-block small">Date</span>
                                            <span class="small">{{ $app->created_at->format('M d') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <a href="{{ route('admin.applicants.show', $app) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-eye me-1"></i>
                                        View Application
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-5">
                        <x-empty-state
                            icon="bi-inbox"
                            title="No applications yet"
                            text="Applications submitted by job seekers will appear here." />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/charts.js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Applications per Month Chart
    const appCtx = document.getElementById('applicationsChart');
    if (appCtx) {
        const months = @json($applicationsPerMonth->pluck('month'));
        const totals = @json($applicationsPerMonth->pluck('total'));

        new Chart(appCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Applications',
                    data: totals,
                    backgroundColor: 'rgba(10, 37, 64, 0.7)',
                    borderColor: 'rgba(10, 37, 64, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Jobs by Category Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        const categories = @json($jobsByCategory->pluck('label'));
        const counts = @json($jobsByCategory->pluck('count'));

        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: categories,
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        '#0A2540', '#E3A008', '#334155', '#C99700',
                        '#475569', '#F0D494', '#0F766E', '#A16207'
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 12, usePointStyle: true }
                    }
                }
            }
        });
    }

    // Hide chart skeletons once both charts have rendered.
    chartReady();
});
</script>
@endpush