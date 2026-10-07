<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $query = Application::with(['user.profile', 'jobVacancy'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('job_vacancy_id')) {
            $query->where('job_vacancy_id', $request->job_vacancy_id);
        }

        $applications = $query->paginate(15)->withQueryString();
        $vacancies = JobVacancy::where('is_active', true)->get();

        return view('admin.applicants.index', compact('applications', 'vacancies'));
    }

    public function show(Application $application)
    {
        $application->load(['user.profile', 'jobVacancy.category', 'interview']);
        return view('admin.applicants.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', Application::statuses())],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
        ]);

        // Decrement slots_available when application is accepted
        if ($request->status === 'accepted') {
            $application->jobVacancy->decrement('slots_available');
        }

        // Create notification for the job seeker
        $application->user->notifications()->create([
            'type' => 'application_status',
            'title' => 'Application Status Updated',
            'message' => "Your application for \"{$application->jobVacancy->title}\" has been updated to: " . ucwords(str_replace('_', ' ', $request->status)),
            'icon' => 'bi-bell-fill',
            'color' => 'primary',
            'action_url' => route('jobseeker.applications.show', $application),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'status_label' => $application->fresh()->status_label,
            'status_color' => $application->fresh()->status_color,
        ]);
    }

    public function viewResume(Application $application)
    {
        $resumePath = $this->resumePath($application);

        if (!$resumePath) {
            return redirect()->back()->with('error', 'No resume found for this applicant.');
        }

        // Serve the file inline so it can be embedded in the preview modal without downloading.
        return Storage::disk('public')->response($resumePath, $this->resumeFilename($application));
    }

    public function downloadResume(Application $application)
    {
        $resumePath = $this->resumePath($application);

        if (!$resumePath) {
            return redirect()->back()->with('error', 'No resume found for this applicant.');
        }

        return Storage::disk('public')->download($resumePath, $this->resumeFilename($application));
    }

    /**
     * Resolve the applicant's resume path on the public disk, or null when missing.
     */
    private function resumePath(Application $application): ?string
    {
        $resumePath = $application->resume_path ?? $application->user->profile?->resume_path;

        return $resumePath && Storage::disk('public')->exists($resumePath) ? $resumePath : null;
    }

    private function resumeFilename(Application $application): string
    {
        return 'resume_' . str_replace(' ', '_', $application->user->name) . '.pdf';
    }
}
