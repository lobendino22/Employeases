@extends('layouts.admin')

@section('title', 'Applicants')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Applicants']]" />
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold">Applicants</h4>
            <p class="text-muted mb-0">Manage job applicants and applications</p>
        </div>
        <div class="d-flex gap-2 w-100 w-md-auto">
            <span class="badge bg-primary-subtle text-primary-emphasis">
                {{ $applications->total() }} {{ Str::plural('applicant', $applications->total()) }}
            </span>
        </div>
    </div>

    <div class="row g-4">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-funnel"></i>
                        Filter Applicants
                    </h6>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="{{ route('admin.applicants.index') }}" class="d-flex flex-column gap-3">
                        <!-- Search -->
                        <div>
                            <label class="form-label text-muted small fw-medium mb-2">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" name="search" class="form-control border-start-0" placeholder="Name or email..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="form-label text-muted small fw-medium mb-2">Application Status</label>
                            <select name="status" class="form-select auto-submit">
                                <option value="">All Status</option>
                                @foreach(['pending','under_review','shortlisted','interview_scheduled','accepted','rejected'] as $s)
                                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Job Filter -->
                        <div>
                            <label class="form-label text-muted small fw-medium mb-2">Job Position</label>
                            <select name="job_vacancy_id" class="form-select auto-submit">
                                <option value="">All Jobs</option>
                                @foreach($vacancies as $v)
                                    <option value="{{ $v->id }}" {{ request('job_vacancy_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'status', 'job_vacancy_id']))
                            <div class="pt-2 border-top">
                                <a href="{{ route('admin.applicants.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Clear All Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($applications->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-semibold">Applicant</th>
                                            <th class="border-0 fw-semibold">Position</th>
                                            <th class="border-0 fw-semibold">Applied Date</th>
                                            <th class="border-0 fw-semibold">Status</th>
                                            <th class="border-0 fw-semibold text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applications as $app)
                                            <tr class="border-bottom">
                                                <td class="py-3">
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
                                                        {{ Str::limit($app->jobVacancy->title, 30) }}
                                                    </a>
                                                </td>
                                                <td class="py-3">
                                                    <span class="text-muted">{{ $app->created_at->format('M d, Y') }}</span>
                                                    <br>
                                                    <small class="text-muted">{{ $app->created_at->format('h:i A') }}</small>
                                                </td>
                                                <td class="py-3">
                                                    <x-status-badge :status="$app->status" :label="$app->status_label" />
                                                </td>
                                                <td class="py-3 text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('admin.applicants.show', $app) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        @if($app->resume_path || $app->user->profile?->resume_path)
                                                            <a href="{{ route('admin.applicants.download-resume', $app) }}" class="btn btn-sm btn-outline-info" title="Download Resume">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-md-none">
                            @foreach($applications as $app)
                                <div class="border-bottom p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;font-size:16px">
                                            {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="mb-1 fw-semibold text-truncate">{{ $app->user->name }}</h6>
                                            <p class="mb-1 text-muted small text-truncate">{{ $app->user->email }}</p>
                                            <div class="mb-2">
                                                <x-status-badge :status="$app->status" :label="$app->status_label" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row g-2 text-sm">
                                            <div class="col-6">
                                                <span class="text-muted d-block small">Position</span>
                                                <a href="{{ route('admin.job-vacancies.show', $app->jobVacancy) }}" class="text-decoration-none fw-medium small">
                                                    {{ $app->jobVacancy->title }}
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block small">Applied</span>
                                                <span class="small">{{ $app->created_at->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.applicants.show', $app) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </a>
                                        @if($app->resume_path || $app->user->profile?->resume_path)
                                            <a href="{{ route('admin.applicants.download-resume', $app) }}" class="btn btn-sm btn-outline-info flex-fill">
                                                <i class="bi bi-download me-1"></i>
                                                Resume
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($applications->hasPages())
                            <div class="p-3 border-top bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} results
                                    </small>
                                    <div>
                                        {{ $applications->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="p-5">
                            <x-empty-state
                                icon="bi-inbox"
                                title="No applicants found"
                                text="Try adjusting your search or filters, or check back once job seekers start applying." />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        let debounceTimer;

        // Dropdowns: submit immediately on change
        document.querySelectorAll('.auto-submit').forEach(function (select) {
            select.addEventListener('change', function () {
                form.submit();
            });
        });

        // Search box: submit 500ms after the user stops typing
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function () {
                form.submit();
            }, 500);
        });

        // Keep focus in the search box after the page reloads
        if (searchInput.value) {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }
    });
</script>
@endsection