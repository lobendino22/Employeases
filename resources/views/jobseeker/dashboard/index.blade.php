@extends('layouts.jobseeker')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
    <div>
        <h4 class="mb-1 fw-bold">Welcome, {{ auth()->user()->name }}!</h4>
        <p class="text-muted mb-0">Your employment journey starts here</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-2 mb-3">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm bg-brand-navy-deep text-white">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase-fill fs-3 opacity-75"></i>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">{{ $savedJobsCount }}</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">Saved Jobs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm bg-brand-gold text-dark">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-text-fill fs-3 opacity-75"></i>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $applicationsCount }}</h5>
                        <small style="font-size: 0.75rem;">Applications</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card border-0 shadow-sm bg-brand-navy text-white">
            <div class="card-body py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill fs-3 opacity-75"></i>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">{{ $unreadNotifications }}</h5>
                        <small class="text-white-50" style="font-size: 0.75rem;">Notifications</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4 mb-3 mb-md-4">
    <!-- Application Statistics -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>
                    <span class="d-none d-sm-inline">Application Status</span>
                    <span class="d-inline d-sm-none">Status</span>
                </h5>
            </div>
            <div class="card-body">
                @if($applicationStats->count() > 0)
                    <div class="mb-3">
                        <canvas id="appStatusChart" style="max-height: 200px;"></canvas>
                    </div>
                    <div class="mt-3">
                        @foreach($applicationStats as $stat)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-{{ (new \App\Models\Application)->fill(['status'=>$stat->status])->status_color }}">
                                    {{ ucwords(str_replace('_', ' ', $stat->status)) }}
                                </span>
                                <span class="fw-bold">{{ $stat->total }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <p class="mb-0">No applications yet</p>
                        <a href="{{ route('jobseeker.jobs.index') }}" class="btn btn-primary btn-sm mt-2">Browse Jobs</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Applications -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                <h5 class="fw-semibold mb-0 d-flex align-items-center">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    <span class="d-none d-sm-inline">Recent Applications</span>
                    <span class="d-inline d-sm-none">Recent</span>
                </h5>
                <a href="{{ route('jobseeker.applications.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentApplications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentApplications as $app)
                            <a href="{{ route('jobseeker.applications.show', $app) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-medium">{{ $app->jobVacancy->title }}</p>
                                        <small class="text-muted">{{ $app->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <p class="mb-0">No recent applications</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Jobs -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
        <h5 class="fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-stars me-2 text-primary"></i>
            <span class="d-none d-sm-inline">Latest Job Opportunities</span>
            <span class="d-inline d-sm-none">Latest Jobs</span>
        </h5>
        <a href="{{ route('jobseeker.jobs.index') }}" class="btn btn-sm btn-primary">Browse All</a>
    </div>
    <div class="card-body">
        @if($recentJobs->count() > 0)
            <div class="row g-3">
                @foreach($recentJobs as $job)
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-2 gap-2">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $job->category->name }}</span>
                                    <small class="text-muted">{{ $job->employment_type_label }}</small>
                                </div>
                                <h6 class="card-title fw-semibold mb-2">
                                    <a href="{{ route('jobseeker.jobs.show', $job) }}" class="text-decoration-none stretched-link">{{ $job->title }}</a>
                                </h6>
                                <p class="card-text text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $job->location }}
                                    @if($job->salary_min)
                                        <br><i class="bi bi-cash me-1"></i>{{ $job->salary_formatted }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No job vacancies available at the moment</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/charts.js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('appStatusChart');
    if (ctx) {
        const labels = @json($applicationStats->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s))));
        const data = @json($applicationStats->pluck('total'));
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#ffc107','#E3A008','#0A2540','#6f42c1','#198754','#dc3545'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: { size: window.innerWidth < 576 ? 10 : 11 },
                            boxWidth: window.innerWidth < 576 ? 12 : 15
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
