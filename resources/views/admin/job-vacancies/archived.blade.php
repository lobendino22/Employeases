@extends('layouts.admin')

@section('title', 'Archived Job Vacancies')

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Job Vacancies', 'url' => route('admin.job-vacancies.index')],
        ['label' => 'Archived'],
    ]" />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Archived Job Vacancies</h4>
        <p class="text-muted mb-0">Vacancies are archived automatically once their application deadline passes</p>
    </div>
    <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Vacancies
    </a>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-2" role="alert">
    <i class="bi bi-info-circle-fill fs-5"></i>
    <div class="small">
        Restoring keeps the original deadline. If that deadline has already passed, update it from the vacancy form so the posting is not archived again.
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.job-vacancies.archived') }}" class="row g-2 live-filter" data-live-filter-target="archived-results">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control" placeholder="Search title, category, location..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="small text-muted mt-2"><i class="bi bi-stars me-1"></i>Results update automatically as you type or change a selection.</div>
    </div>
</div>

<!-- Archived Vacancies List -->
<div id="archived-results" class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($vacancies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Deadline</th>
                            <th>Archived On</th>
                            <th>Applicants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vacancies as $vacancy)
                            <tr>
                                <td data-label="Title">
                                    <a href="{{ route('admin.job-vacancies.show', $vacancy) }}" class="text-decoration-none fw-medium">
                                        {{ $vacancy->title }}
                                    </a>
                                    <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $vacancy->location }}</div>
                                </td>
                                <td data-label="Category"><span class="badge bg-light text-dark">{{ $vacancy->category?->name ?? 'N/A' }}</span></td>
                                <td data-label="Type"><span class="badge bg-primary-subtle text-primary-emphasis">{{ $vacancy->employment_type_label }}</span></td>
                                <td data-label="Deadline" class="small">
                                    @if($vacancy->application_deadline)
                                        {{ $vacancy->application_deadline->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">No deadline</span>
                                    @endif
                                </td>
                                <td data-label="Archived On" class="small">{{ $vacancy->archived_at?->format('M d, Y g:i A') ?? 'N/A' }}</td>
                                <td data-label="Applicants">
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                        {{ $vacancy->applicants_count }}
                                    </span>
                                </td>
                                <td data-label="Actions">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.job-vacancies.show', $vacancy) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.job-vacancies.restore', $vacancy->id) }}" method="POST" class="d-inline confirm-form"
                                              data-confirm-title="Restore this vacancy?"
                                              data-confirm-text="The vacancy will be moved back to the active job vacancies list."
                                              data-confirm-ok="Yes, restore it"
                                              data-confirm-color="#198754">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.job-vacancies.force-delete', $vacancy->id) }}" method="POST" class="d-inline confirm-form"
                                              data-confirm-title="Permanently delete this vacancy?"
                                              data-confirm-text="This removes the vacancy and its {{ $vacancy->applicants_count }} application(s) for good. This cannot be undone."
                                              data-confirm-ok="Yes, delete forever"
                                              data-confirm-color="#dc3545">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Permanently delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vacancies->hasPages())
                <div class="p-3 border-top">
                    {{ $vacancies->links() }}
                </div>
            @endif
        @else
            <x-empty-state
                icon="bi-archive"
                title="No archived vacancies"
                text="Job vacancies are archived automatically once their application deadline passes."
                :action-url="route('admin.job-vacancies.index')"
                action-label="Back to Job Vacancies" />
        @endif
    </div>
</div>
@endsection
