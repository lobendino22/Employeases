@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Notifications</h4>
        <p class="text-muted mb-0">Updates on applications, interviews and system activity</p>
    </div>
    @if(auth()->user()->unreadNotifications()->count() > 0)
        <button class="btn btn-primary mark-all-read" type="button">
            <i class="bi bi-check-all me-1"></i> Mark All as Read
        </button>
    @endif
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($notifications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="notification-icon mt-1">
                                <i class="bi {{ $notification->icon }} text-{{ $notification->color }} fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(!$notification->is_read)
                                        <button class="btn btn-sm btn-link text-decoration-none mark-read p-0" data-id="{{ $notification->id }}" title="Mark as read">
                                            <i class="bi bi-check-circle text-primary"></i>
                                        </button>
                                    @endif
                                </div>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="btn btn-sm btn-outline-primary mt-2">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-3 border-top">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                <h5>No Notifications</h5>
                <p>System and applicant activity will appear here</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.mark-read').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('{{ url('/admin/notifications') }}/' + id + '/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                this.closest('.list-group-item').classList.remove('bg-light');
                this.remove();
            }
        });
    });
});

document.querySelector('.mark-all-read')?.addEventListener('click', function() {
    fetch('{{ route('admin.notifications.mark-all-read') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.querySelectorAll('.list-group-item').forEach(el => el.classList.remove('bg-light'));
            document.querySelectorAll('.mark-read').forEach(el => el.remove());
            this.remove();
            Swal.fire({ icon: 'success', title: 'Done!', text: 'All notifications marked as read', timer: 1500, showConfirmButton: false });
        }
    });
});
</script>
@endpush
