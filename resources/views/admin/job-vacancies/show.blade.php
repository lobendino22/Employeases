@extends('layouts.admin')

@section('title', $jobVacancy->title)

@section('breadcrumbs')
    <x-breadcrumbs :items="[
        ['label' => 'Job Vacancies', 'url' => route('admin.job-vacancies.index')],
        ['label' => $jobVacancy->title],
    ]" />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">{{ $jobVacancy->title }}</h4>
        <p class="text-muted mb-0">
            <i class="bi bi-geo-alt me-1"></i>{{ $jobVacancy->location }}
            &middot; <i class="bi bi-building me-1"></i>{{ $jobVacancy->company ?? 'N/A' }}
        </p>
    </div>
    <div class="d-flex gap-2">
        @unless($jobVacancy->trashed())
            <a href="{{ route('admin.job-vacancies.edit', $jobVacancy) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        @endunless
        <a href="{{ route('admin.job-vacancies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if($jobVacancy->trashed())
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-archive-fill fs-5"></i>
        <div class="flex-grow-1">
            <strong>This vacancy is archived.</strong>
            <span class="d-block small">
                Archived on {{ $jobVacancy->archived_at?->format('M d, Y g:i A') ?? 'N/A' }}. Restore it to make it editable and visible again.
            </span>
        </div>
        <form action="{{ route('admin.job-vacancies.restore', $jobVacancy->id) }}" method="POST" class="confirm-form"
              data-confirm-title="Restore this vacancy?"
              data-confirm-text="The vacancy will be moved back to the active job vacancies list."
              data-confirm-ok="Yes, restore it"
              data-confirm-color="#198754">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
            </button>
        </form>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-file-text me-2 text-primary"></i>Job Description</h5>
                <p class="text-muted">{{ $jobVacancy->description }}</p>

                @if($jobVacancy->requirements)
                    <h5 class="fw-semibold mt-4 mb-3"><i class="bi bi-list-check me-2 text-primary"></i>Requirements</h5>
                    <p class="text-muted">{{ $jobVacancy->requirements }}</p>
                @endif

                @if($jobVacancy->benefits)
                    <h5 class="fw-semibold mt-4 mb-3"><i class="bi bi-gift me-2 text-primary"></i>Benefits</h5>
                    <p class="text-muted">{{ $jobVacancy->benefits }}</p>
                @endif
            </div>
        </div>

        <!-- Applicants for this job -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-semibold mb-0">
                    <i class="bi bi-people me-2 text-primary"></i>Applicants ({{ $jobVacancy->applications->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if($jobVacancy->applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Applied</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobVacancy->applications as $app)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:35px;height:35px;font-size:13px">
                                                    {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-medium">{{ $app->user->name }}</p>
                                                    <small class="text-muted">{{ $app->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $app->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <x-status-badge :status="$app->status" :label="$app->status_label" />
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.applicants.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-empty-state
                        icon="bi-inbox"
                        title="No applicants yet"
                        text="Applications for this vacancy will appear here once job seekers apply." />
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Details</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        @if($jobVacancy->trashed())
                            <x-status-badge status="archived" label="Archived" />
                        @elseif($jobVacancy->is_open && $jobVacancy->is_active)
                            <x-status-badge status="open" label="Open" />
                        @elseif(!$jobVacancy->is_open)
                            <x-status-badge status="closed" label="Closed" />
                        @else
                            <x-status-badge status="inactive" label="Inactive" />
                        @endif
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Category</span>
                        <span>{{ $jobVacancy->category?->name ?? 'N/A' }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Type</span>
                        <span>{{ $jobVacancy->employment_type_label }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Salary</span>
                        <span class="fw-medium">{{ $jobVacancy->salary_formatted }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Slots</span>
                        <span>{{ $jobVacancy->slots_available }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Applicants</span>
                        <span>{{ $jobVacancy->applicants_count }}</span>
                    </li>
                    <li class="mb-3 d-flex justify-content-between">
                        <span class="text-muted">Deadline</span>
                        <span>
                            @if($jobVacancy->application_deadline)
                                {{ $jobVacancy->application_deadline->format('M d, Y') }}
                            @else
                                No deadline
                            @endif
                        </span>
                    </li>
                    <li class="d-flex justify-content-between">
                        <span class="text-muted">Created</span>
                        <span>{{ $jobVacancy->created_at->format('M d, Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-gear me-2 text-primary"></i>Actions</h5>
                <div class="d-grid gap-2">
                    @if($jobVacancy->trashed())
                        <form action="{{ route('admin.job-vacancies.restore', $jobVacancy->id) }}" method="POST" class="confirm-form"
                              data-confirm-title="Restore this vacancy?"
                              data-confirm-text="The vacancy will be moved back to the active job vacancies list."
                              data-confirm-ok="Yes, restore it"
                              data-confirm-color="#198754">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Vacancy
                            </button>
                        </form>
                        <form action="{{ route('admin.job-vacancies.force-delete', $jobVacancy->id) }}" method="POST" class="confirm-form"
                              data-confirm-title="Permanently delete this vacancy?"
                              data-confirm-text="This removes the vacancy and its {{ $jobVacancy->applications->count() }} application(s) for good. This cannot be undone."
                              data-confirm-ok="Yes, delete forever"
                              data-confirm-color="#dc3545">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash me-1"></i> Permanently Delete
                            </button>
                        </form>
                    @else
                    <form action="{{ route('admin.job-vacancies.toggle-status', $jobVacancy) }}" method="POST" class="confirm-form"
                          data-confirm-title="{{ $jobVacancy->is_open ? 'Close this vacancy?' : 'Reopen this vacancy?' }}"
                          data-confirm-text="{{ $jobVacancy->is_open ? 'Applicants will no longer be able to apply for this position.' : 'This vacancy will be reopened for applications.' }}"
                          data-confirm-ok="{{ $jobVacancy->is_open ? 'Yes, close it' : 'Yes, reopen it' }}"
                          data-confirm-color="#d97706">
                        @csrf
                        <button type="submit" class="btn btn-{{ $jobVacancy->is_open ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-{{ $jobVacancy->is_open ? 'lock' : 'unlock' }} me-1"></i>
                            {{ $jobVacancy->is_open ? 'Close Vacancy' : 'Open Vacancy' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.job-vacancies.destroy', $jobVacancy) }}" method="POST" class="delete-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-archive me-1"></i> Archive Vacancy
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('.delete-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Archive Vacancy?',
        text: 'The vacancy will be moved to the archive, where you can restore or permanently delete it.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, archive it!'
    }).then(result => { if (result.isConfirmed) this.submit(); });
});
</script>
@endpush
