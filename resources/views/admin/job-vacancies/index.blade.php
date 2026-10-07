@extends('layouts.admin')

@section('title', 'Job Vacancies')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Job Vacancies']]" />
@endsection

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mb-md-4 gap-2">
    <div>
        <h4 class="mb-1 fw-bold">Job Vacancies</h4>
        <p class="text-muted mb-0">Manage all job vacancy postings</p>
    </div>
    <div class="d-flex gap-2 w-100 w-md-auto">
        <a href="{{ route('admin.job-vacancies.archived') }}" class="btn btn-outline-secondary flex-grow-1 flex-md-grow-0">
            <i class="bi bi-archive me-1"></i> <span class="d-none d-sm-inline">Archived</span>
            @if(($archivedCount ?? 0) > 0)
                <span class="badge bg-secondary rounded-pill ms-1">{{ $archivedCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.job-vacancies.create') }}" class="btn btn-primary flex-grow-1 flex-md-grow-0">
            <i class="bi bi-plus-lg me-1"></i> <span class="d-none d-sm-inline">Create New</span>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3 mb-md-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.job-vacancies.index') }}" class="row g-2 live-filter" data-live-filter-target="vacancy-results">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="employment_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                    <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="deadline" class="form-select">
                    <option value="">Any Deadline</option>
                    <option value="closing_soon" {{ request('deadline') == 'closing_soon' ? 'selected' : '' }}>Closing Soon</option>
                    <option value="no_deadline" {{ request('deadline') == 'no_deadline' ? 'selected' : '' }}>No Deadline</option>
                </select>
            </div>
        </form>
        <div class="small text-muted mt-2"><i class="bi bi-stars me-1"></i>Results update automatically</div>
    </div>
</div>

<!-- Vacancies List -->
<div id="vacancy-results" class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($vacancies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th class="d-none d-lg-table-cell">Type</th>
                            <th class="d-none d-xl-table-cell">Location</th>
                            <th class="d-none d-xl-table-cell">Salary</th>
                            <th class="d-none d-md-table-cell">Applicants</th>
                            <th class="d-none d-lg-table-cell">Deadline</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Slots</th>
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
                                    <div class="d-md-none small text-muted mt-1">
                                        <div><i class="bi bi-geo-alt me-1"></i>{{ $vacancy->location }}</div>
                                        <div><i class="bi bi-cash me-1"></i>{{ $vacancy->salary_formatted }}</div>
                                    </div>
                                </td>
                                <td data-label="Category">
                                    <span class="badge bg-light text-dark">{{ $vacancy->category?->name ?? 'N/A' }}</span>
                                    <div class="d-lg-none mt-1">
                                        <span class="badge bg-primary-subtle text-primary-emphasis">{{ $vacancy->employment_type_label }}</span>
                                    </div>
                                </td>
                                <td data-label="Type" class="d-none d-lg-table-cell">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $vacancy->employment_type_label }}</span>
                                </td>
                                <td data-label="Location" class="d-none d-xl-table-cell">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $vacancy->location }}
                                </td>
                                <td data-label="Salary" class="d-none d-xl-table-cell small">
                                    {{ $vacancy->salary_formatted }}
                                </td>
                                <td data-label="Applicants" class="d-none d-md-table-cell">
                                    <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">
                                        {{ $vacancy->applicants_count }}
                                    </span>
                                </td>
                                <td data-label="Deadline" class="d-none d-lg-table-cell small">
                                    @if($vacancy->application_deadline)
                                        <div>{{ $vacancy->application_deadline->format('M d, Y') }}</div>
                                        @php($daysLeft = $vacancy->daysUntilDeadline())
                                        @if($vacancy->isClosingSoon())
                                            <span class="badge bg-warning-subtle text-warning-emphasis" title="Deadline is approaching">
                                                <i class="bi bi-clock-history me-1"></i>
                                                {{ $daysLeft === 0 ? 'Today' : $daysLeft . 'd' }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No deadline</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    @if($vacancy->is_open && $vacancy->is_active)
                                        <x-status-badge status="open" label="Active" />
                                    @elseif(!$vacancy->is_open)
                                        <x-status-badge status="closed" label="Closed" />
                                    @else
                                        <x-status-badge status="inactive" label="Inactive" />
                                    @endif
                                    <div class="d-lg-none mt-1">
                                        @if($vacancy->slots_available > 0)
                                            <span class="badge bg-success-subtle text-success-emphasis">
                                                {{ $vacancy->slots_available }} slots
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger-emphasis">
                                                Full
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Slots" class="d-none d-lg-table-cell small">
                                    @if($vacancy->slots_available > 0)
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            {{ $vacancy->slots_available }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger-emphasis">
                                            Full
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Actions">
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.job-vacancies.edit', $vacancy) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i><span class="d-md-none ms-1">Edit</span>
                                        </a>
                                        <form action="{{ route('admin.job-vacancies.toggle-status', $vacancy) }}" method="POST" class="d-inline confirm-form"
                                              data-confirm-title="{{ $vacancy->is_open ? 'Close this vacancy?' : 'Reopen this vacancy?' }}"
                                              data-confirm-text="{{ $vacancy->is_open ? 'Applicants will no longer be able to apply.' : 'This vacancy will be reopened for applications.' }}"
                                              data-confirm-ok="{{ $vacancy->is_open ? 'Yes, close it' : 'Yes, reopen it' }}"
                                              data-confirm-color="#d97706">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $vacancy->is_open ? 'danger' : 'success' }}"
                                                    title="{{ $vacancy->is_open ? 'Close' : 'Open' }}">
                                                <i class="bi bi-{{ $vacancy->is_open ? 'lock' : 'unlock' }}"></i><span class="d-md-none ms-1">{{ $vacancy->is_open ? 'Close' : 'Open' }}</span>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.job-vacancies.destroy', $vacancy) }}" method="POST" class="d-inline delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Archive">
                                                <i class="bi bi-archive"></i><span class="d-md-none ms-1">Archive</span>
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
                icon="bi-briefcase"
                title="No job vacancies yet"
                text="Post your first vacancy to start receiving applications from local job seekers."
                :action-url="route('admin.job-vacancies.create')"
                action-label="Create First Vacancy" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Delegated so it still applies to rows rendered by the live filter.
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.classList.contains('delete-form') || form.dataset.confirmed) {
        return;
    }
    e.preventDefault();
    Swal.fire({
        title: 'Archive Vacancy?',
        text: 'The vacancy will be moved to the archive, where you can restore or permanently delete it.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, archive it!'
    }).then(result => {
        if (result.isConfirmed) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    });
});
</script>
@endpush
