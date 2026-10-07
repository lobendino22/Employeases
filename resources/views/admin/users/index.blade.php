@extends('layouts.admin')

@section('title', 'Manage Job Seekers')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Job Seekers']]" />
@endsection

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
        <div>
            <h4 class="mb-1 fw-bold">Job Seekers</h4>
            <p class="text-muted mb-0">Manage registered job seeker accounts</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary-emphasis p-2 fw-medium">
                {{ $users->total() }} {{ Str::plural('job seeker', $users->total()) }}
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
                        Filter Job Seekers
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-column gap-3">
                        <!-- Search -->
                        <div>
                            <label class="form-label text-muted small fw-medium mb-2">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Name or email..." value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="form-label text-muted small fw-medium mb-2">Account Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel me-1"></i>
                                Apply Filters
                            </button>
                        </div>

                        <!-- Clear Filters -->
                        @if(request()->hasAny(['search', 'status']))
                            <div class="border-top pt-2">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Job Seekers List -->
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <!-- Desktop Table View -->
                        <div class="d-none d-md-block">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 fw-semibold">Name</th>
                                            <th class="border-0 fw-semibold">Email</th>
                                            <th class="border-0 fw-semibold">Joined</th>
                                            <th class="border-0 fw-semibold">Applications</th>
                                            <th class="border-0 fw-semibold">Status</th>
                                            <th class="border-0 fw-semibold text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr class="border-bottom">
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;font-size:14px">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                        <div class="min-w-0">
                                                            <p class="mb-0 fw-medium text-truncate">{{ $user->name }}</p>
                                                            <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <span class="text-truncate d-block">{{ $user->email }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td class="py-3">
                                                    <span class="badge bg-info-subtle text-info-emphasis">
                                                        {{ $user->applications()->count() }} applications
                                                    </span>
                                                </td>
                                                <td class="py-3">
                                                    <x-status-badge :status="$user->is_active ? 'active' : 'inactive'" :label="$user->is_active ? 'Active' : 'Inactive'" />
                                                </td>
                                                <td class="py-3 text-end">
                                                    <div class="d-flex gap-1 justify-content-end">
                                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline confirm-form"
                                                              data-confirm-title="{{ $user->is_active ? 'Deactivate this account?' : 'Activate this account?' }}"
                                                              data-confirm-text="{{ $user->is_active ? 'The job seeker will no longer be able to sign in.' : 'The job seeker will be able to sign in again.' }}"
                                                              data-confirm-ok="{{ $user->is_active ? 'Yes, deactivate' : 'Yes, activate' }}"
                                                              data-confirm-color="#d97706">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                                    title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                                <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                                            </button>
                                                        </form>
                                                        @if(!$user->isAdmin())
                                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
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
                            @foreach($users as $user)
                                <div class="border-bottom p-3">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;font-size:16px">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="mb-1 fw-semibold text-truncate">{{ $user->name }}</h6>
                                            <p class="mb-2 text-muted small text-truncate">{{ $user->email }}</p>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                <x-status-badge :status="$user->is_active ? 'active' : 'inactive'" :label="$user->is_active ? 'Active' : 'Inactive'" />
                                                <span class="badge bg-info-subtle text-info-emphasis small">
                                                    {{ $user->applications()->count() }} apps
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <span class="text-muted d-block small">Joined</span>
                                                <span class="small">{{ $user->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block small">Applications</span>
                                                <span class="small">{{ $user->applications()->count() }} submitted</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </a>
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="confirm-form flex-fill"
                                              data-confirm-title="{{ $user->is_active ? 'Deactivate this account?' : 'Activate this account?' }}"
                                              data-confirm-text="{{ $user->is_active ? 'The job seeker will no longer be able to sign in.' : 'The job seeker will be able to sign in again.' }}"
                                              data-confirm-ok="{{ $user->is_active ? 'Yes, deactivate' : 'Yes, activate' }}"
                                              data-confirm-color="#d97706">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }} w-100">
                                                <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="p-3 border-top bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                                    </small>
                                    <div>
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="p-5">
                            <x-empty-state
                                icon="bi-people"
                                title="No job seekers found"
                                text="Try adjusting your search or filters, or check back once new users register." />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Delete User?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush
