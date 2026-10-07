@extends('layouts.jobseeker')

@section('title', 'Browse Jobs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Browse Jobs</h4>
        <p class="text-muted mb-0">Find your next employment opportunity</p>
    </div>
    <a href="{{ route('jobseeker.jobs.saved') }}" class="btn btn-outline-primary btn-saved-jobs">
        <i class="bi bi-bookmark-heart me-1"></i> Saved Jobs
    </a>
</div>

<!-- Search & Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('jobseeker.jobs.index') }}">
            <div class="filter-row-mobile">
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search jobs, keywords..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <select name="employment_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ request('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <input type="text" name="location" class="form-control" placeholder="Location" value="{{ request('location') }}">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Jobs Grid -->
@if($jobs->count() > 0)
    <div class="row g-3">
        @foreach($jobs as $job)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100 job-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge cat-badge cat-{{ $job->category->slug }}">{{ $job->category->name }}</span>
                            <button class="btn btn-sm btn-link text-muted save-job-btn p-0" data-job-id="{{ $job->id }}"
                                    title="{{ in_array($job->id, $savedJobIds) ? 'Unsave' : 'Save' }}">
                                <i class="bi bi-bookmark{{ in_array($job->id, $savedJobIds) ? '-fill' : '' }} fs-5 save-icon"></i>
                            </button>
                        </div>
                        <h5 class="card-title fw-semibold mb-1">
                            <a href="{{ route('jobseeker.jobs.show', $job) }}" class="text-decoration-none stretched-link">{{ $job->title }}</a>
                        </h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-building me-1"></i>{{ $job->company ?? 'N/A' }}
                        </p>
                        <div class="d-flex flex-wrap gap-2 mb-3 small">
                            <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</span>
                            <span class="text-muted"><i class="bi bi-clock me-1"></i>{{ $job->employment_type_label }}</span>
                            @if($job->salary_min)
                                <span class="text-primary-emphasis fw-medium"><i class="bi bi-cash me-1"></i>{{ $job->salary_formatted }}</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                @if($job->application_deadline)
                                    Posting Closing Date: {{ $job->application_deadline->format('M d, Y') }}
                                @else
                                    No deadline
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $jobs->links() }}
    </div>
@else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-search fs-1 d-block mb-2"></i>
        <h5>No Jobs Found</h5>
        <p>Try adjusting your search or filter criteria</p>
        <a href="{{ route('jobseeker.jobs.index') }}" class="btn btn-primary btn-sm">Clear Filters</a>
    </div>
@endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.save-job-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const jobId = this.dataset.jobId;
        const icon = this.querySelector('.save-icon');
        fetch('/jobseeker/jobs/' + jobId + '/toggle-save', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                if (data.saved) {
                    icon.classList.add('bi-bookmark-fill');
                    icon.classList.remove('bi-bookmark');
                    Swal.fire({ icon: 'success', title: 'Saved!', text: 'Job saved successfully', timer: 1500, showConfirmButton: false });
                } else {
                    icon.classList.remove('bi-bookmark-fill');
                    icon.classList.add('bi-bookmark');
                    Swal.fire({ icon: 'info', title: 'Removed', text: 'Job removed from saved', timer: 1500, showConfirmButton: false });
                }
            }
        });
    });
});
</script>
@endpush
