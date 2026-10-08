<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScheduleInterviewRequest;
use App\Models\Application;
use App\Models\Interview;
use App\Notifications\InterviewRescheduled;
use App\Notifications\InterviewScheduled;
use App\Notifications\InterviewStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Interview::with(['application.user', 'application.jobVacancy'])->latest('scheduled_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $interviews = $query->paginate(15)->withQueryString();

        return view('admin.interviews.index', compact('interviews'));
    }

    public function create(Application $application)
    {
        $application->load(['user', 'jobVacancy']);
        return view('admin.interviews.create', compact('application'));
    }

    public function store(ScheduleInterviewRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $interview = Interview::create($data);

        // Update application status to interview_scheduled
        $application = Application::findOrFail($data['application_id']);
        $application->update([
            'status' => 'interview_scheduled',
            'reviewed_at' => now(),
        ]);

        // Notify the job seeker
        $application->user->notifications()->create([
            'type' => 'interview_scheduled',
            'title' => 'Interview Scheduled',
            'message' => "An interview has been scheduled for \"{$application->jobVacancy->title}\" on " . $interview->scheduled_at->format('F d, Y h:i A'),
            'icon' => 'bi-calendar-event-fill',
            'color' => 'success',
            'action_url' => route('jobseeker.applications.show', $application),
        ]);

        // Email the job seeker about the interview.
        try {
            $interview->loadMissing('application.jobVacancy');
            $application->user->notify(new InterviewScheduled($interview));
        } catch (\Throwable $e) {
            Log::error('Failed to send interview scheduled email', [
                'interview_id' => $interview->id,
                'user_id' => $application->user_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.interviews.index')
            ->with('success', 'Interview scheduled successfully.');
    }

    public function show(Interview $interview)
    {
        $interview->load(['application.user.profile', 'application.jobVacancy']);
        return view('admin.interviews.show', compact('interview'));
    }

    public function edit(Interview $interview)
    {
        $interview->load(['application.user', 'application.jobVacancy']);
        return view('admin.interviews.edit', compact('interview'));
    }

    public function update(ScheduleInterviewRequest $request, Interview $interview)
    {
        $previousStatus = $interview->status;

        $interview->update($request->validated());

        // Notify the job seeker that their interview was rescheduled (edited)
        $this->notifyRescheduled($interview);

        return redirect()->route('admin.interviews.show', $interview)
            ->with('success', 'Interview rescheduled successfully. The jobseeker has been notified by email.');
    }

    public function saveDateTime(Request $request, Interview $interview)
    {
        $request->validate([
            'scheduled_at' => ['required', 'date', 'after:now'],
        ]);

        $interview->update([
            'scheduled_at' => $request->input('scheduled_at'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Interview date & time saved successfully.',
            'scheduled_at' => $interview->scheduled_at->format('Y-m-d\TH:i'),
        ]);
    }

    protected function notifyRescheduled(Interview $interview): void
    {
        try {
            $interview->loadMissing('application.user', 'application.jobVacancy');
            $user = $interview->application->user;

            // In-app notification
            $user->notifications()->create([
                'type' => 'interview_rescheduled',
                'title' => 'Interview Rescheduled',
                'message' => "Your interview for \"{$interview->application->jobVacancy->title}\" has been rescheduled to " . $interview->scheduled_at->format('F d, Y h:i A'),
                'icon' => 'bi-calendar-check',
                'color' => 'warning',
                'action_url' => route('jobseeker.applications.show', $interview->application),
            ]);

            // Email notification
            $user->notify(new InterviewRescheduled($interview));
        } catch (\Throwable $e) {
            Log::error('Failed to send interview reschedule notification', [
                'interview_id' => $interview->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function updateStatus(Request $request, Interview $interview)
    {
        $request->validate([
            'status' => ['required', 'in:scheduled,completed,cancelled,rescheduled'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $previousStatus = $interview->status;

        $interview->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // If the status actually changed, notify the jobseeker
        if ($previousStatus !== $interview->status) {
            try {
                $interview->loadMissing('application.user', 'application.jobVacancy');
                $user = $interview->application->user;

                // In-app notification
                $statusLabel = $interview->status_label;
                $user->notifications()->create([
                    'type' => 'interview_status_updated',
                    'title' => 'Interview Status Updated',
                    'message' => "Your interview status has been updated to: {$statusLabel}" . ($interview->notes ? " — {$interview->notes}" : ''),
                    'icon' => 'bi-info-circle',
                    'color' => 'info',
                    'action_url' => route('jobseeker.applications.show', $interview->application),
                ]);

                // Email notification (skip if status was reset back to "scheduled" — no email needed for that)
                if (in_array($interview->status, ['completed', 'cancelled', 'rescheduled'])) {
                    $user->notify(new InterviewStatusUpdated($interview, $previousStatus));
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send interview status update notification', [
                    'interview_id' => $interview->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Interview status updated successfully.',
        ]);
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();

        return redirect()->route('admin.interviews.index')
            ->with('success', 'Interview deleted successfully.');
    }
}
