@extends('layouts.jobseeker')

@section('title', 'My Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">My Profile</h4>
        <p class="text-muted mb-0">Manage your personal information and resume</p>
    </div>
    <a href="{{ route('jobseeker.profile.edit') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Edit Profile
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center mb-4">
            <div class="card-body p-4">
                @php $photo = $user->profile?->profile_photo_path; @endphp
                @if($photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($photo))
                    <img src="{{ Storage::url($photo) }}" alt="" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;border:3px solid #E3A008">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:120px;height:120px;font-size:40px;font-weight:600">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <p class="text-muted small mb-0">Member since {{ $user->created_at->format('M d, Y') }}</p>

                <a href="{{ route('jobseeker.profile.edit') }}" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="bi bi-camera me-1"></i> Update Profile/Photo
                </a>
            </div>
        </div>

        <!-- Resume Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-3"><i class="bi bi-file-pdf me-1 text-danger"></i> Resume</h6>
                @if($user->profile?->resume_path)
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Resume uploaded</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('jobseeker.profile.download-resume') }}" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-download me-1"></i> Download Resume
                        </a>
                    </div>
                @else
                    <p class="text-muted small mb-2">No resume uploaded yet</p>
                @endif
                <hr>
                <form action="{{ route('jobseeker.profile.upload-resume') }}" method="POST" enctype="multipart/form-data" id="resumeUploadForm">
                    @csrf
                    <label class="form-label small">Upload Resume (PDF only, max 5MB)</label>
                    <input type="file" name="resume" class="form-control form-control-sm mb-2" accept=".pdf" required>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-upload me-1"></i> Upload
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-4"><i class="bi bi-person-vcard me-2 text-primary"></i>Personal Information</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Full Name</small>
                        <p class="fw-medium">{{ $user->name }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Email</small>
                        <p class="fw-medium">{{ $user->email }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Phone</small>
                        <p>{{ $user->profile?->phone ?? 'Not set' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Gender</small>
                        <p>{{ $user->profile?->gender ? ucfirst($user->profile->gender) : 'Not set' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Birth Date</small>
                        <p>{{ $user->profile?->birth_date?->format('M d, Y') ?? 'Not set' }}</p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Address</small>
                        <p>{{ $user->profile?->address ?? 'Not set' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">City</small>
                        <p>{{ $user->profile?->city ?? 'Not set' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Province</small>
                        <p>{{ $user->profile?->province ?? 'Ilocos Sur' }}</p>
                    </div>
                    <div class="col-sm-4">
                        <small class="text-muted d-block">Postal Code</small>
                        <p>{{ $user->profile?->postal_code ?? 'Not set' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($user->profile?->education)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-book me-2 text-primary"></i>Education</h5>
                    <p>{{ $user->profile->education }}</p>
                </div>
            </div>
        @endif

        @if($user->profile?->skills)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-gear me-2 text-primary"></i>Skills</h5>
                    <div>
                        @foreach($user->profile->skills_array as $skill)
                            <span class="badge bg-primary-subtle text-primary-emphasis me-1 mb-1 p-2">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if($user->profile?->work_experience)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-briefcase me-2 text-primary"></i>Work Experience</h5>
                    <p>{{ $user->profile->work_experience }}</p>
                </div>
            </div>
        @endif

        @if(!$user->profile?->education && !$user->profile?->skills && !$user->profile?->work_experience)
            <div class="text-center py-4">
                <p class="text-muted">Complete your profile to increase your chances of getting hired.</p>
                <a href="{{ route('jobseeker.profile.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil-square me-1"></i> Complete Profile
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('resumeUploadForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    Swal.fire({
        title: 'Upload Resume?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Upload'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('{{ route("jobseeker.profile.upload-resume") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) Swal.fire('Uploaded!', data.message, 'success').then(() => location.reload());
                else Swal.fire('Error', 'Upload failed', 'error');
            }).catch(() => Swal.fire('Error', 'Upload failed', 'error'));
        }
    });
});
</script>
@endpush
