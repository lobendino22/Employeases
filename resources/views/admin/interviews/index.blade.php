@extends('layouts.admin')

@section('title', 'Interviews')

@section('breadcrumbs')
    <x-breadcrumbs :items="[['label' => 'Interviews']]" />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Interviews</h4>
        <p class="text-muted mb-0">Manage interview schedules</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" id="filterForm" class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Interviews List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($interviews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Applicant</th>
                            <th>Position</th>
                            <th>Scheduled Date</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($interviews as $interview)
                            <tr>
                                <td data-label="Applicant">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:35px;height:35px;font-size:13px">
                                            {{ strtoupper(substr($interview->application->user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $interview->application->user->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Position">{{ $interview->application->jobVacancy->title }}</td>
                                <td data-label="Scheduled Date">
                                    <span class="fw-medium">{{ $interview->scheduled_at->format('M d, Y') }}</span>
                                    <br><small class="text-muted">{{ $interview->scheduled_at->format('h:i A') }}</small>
                                </td>
                                <td data-label="Type"><span class="badge bg-light text-dark">{{ $interview->type_label }}</span></td>
                                <td data-label="Location">{{ $interview->location }}</td>
                                <td data-label="Status">
                                    <x-status-badge :status="$interview->status" :label="$interview->status_label" />
                                </td>
                                <td data-label="Actions">
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.interviews.show', $interview) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i> <span class="d-md-none">View</span>
                                        </a>
                                        @if($interview->status === 'scheduled')
                                            <a href="{{ route('admin.interviews.edit', $interview) }}" class="btn btn-sm btn-outline-warning" title="Reschedule">
                                                <i class="bi bi-calendar2"></i> <span class="d-md-none">Reschedule</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $interviews->links() }}
            </div>
        @else
            <x-empty-state
                icon="bi-calendar-x"
                title="No interviews scheduled"
                text="Interviews appear here once you schedule them from an applicant's profile." />
        @endif
    </div>
</div>
@endsection
