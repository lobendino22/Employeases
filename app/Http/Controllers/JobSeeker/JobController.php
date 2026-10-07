<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobVacancy::with('category')
            ->available()
            ->where(function ($q) {
                $q->whereNull('application_deadline')
                  ->orWhereDate('application_deadline', '>=', today());
            });

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('employment_type')) {
            $query->byEmploymentType($request->employment_type);
        }
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }
        if ($request->filled('salary_min')) {
            $query->bySalaryRange($request->salary_min, $request->salary_max);
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();
        $categories = JobCategory::where('is_active', true)->get();
        $savedJobIds = auth()->user()->savedJobs()->pluck('job_vacancy_id')->toArray();

        return view('jobseeker.jobs.index', compact('jobs', 'categories', 'savedJobIds'));
    }

    public function show(JobVacancy $jobVacancy)
    {
        if ($jobVacancy->trashed() || !$jobVacancy->is_active || !$jobVacancy->is_open) {
            return redirect()->route('jobseeker.jobs.index')
                ->with('error', 'This job vacancy is no longer available.');
        }

        $jobVacancy->load('category');
        $relatedJobs = JobVacancy::where('job_category_id', $jobVacancy->job_category_id)
            ->where('id', '!=', $jobVacancy->id)
            ->available()
            ->take(4)
            ->get();

        $hasApplied = auth()->user()->applications()
            ->where('job_vacancy_id', $jobVacancy->id)
            ->exists();

        $isSaved = auth()->user()->savedJobs()
            ->where('job_vacancy_id', $jobVacancy->id)
            ->exists();

        return view('jobseeker.jobs.show', compact('jobVacancy', 'relatedJobs', 'hasApplied', 'isSaved'));
    }

    public function toggleSave(JobVacancy $jobVacancy)
    {
        $user = auth()->user();

        if ($user->savedJobs()->where('job_vacancy_id', $jobVacancy->id)->exists()) {
            $user->savedJobs()->detach($jobVacancy->id);
            $saved = false;
        } else {
            $user->savedJobs()->attach($jobVacancy->id);
            $saved = true;
        }

        return response()->json([
            'success' => true,
            'saved' => $saved,
            'message' => $saved ? 'Job saved successfully.' : 'Job removed from saved.',
        ]);
    }

    public function saved()
    {
        $jobs = auth()->user()->savedJobs()
            ->with('category')
            ->available()
            ->latest()
            ->paginate(12);

        return view('jobseeker.jobs.saved', compact('jobs'));
    }
}
