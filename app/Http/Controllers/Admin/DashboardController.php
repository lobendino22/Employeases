<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVacancies = JobVacancy::count();
        $totalApplicants = Application::count();
        $activeJobs = JobVacancy::where('is_active', true)->where('is_open', true)->count();
        $closedJobs = JobVacancy::where('is_open', false)->count();

        $recentApplications = Application::with(['user', 'jobVacancy'])
            ->latest()
            ->take(5)
            ->get();

        $applicationsPerMonth = Application::select(
            DB::raw("DATE_FORMAT(created_at, '%m') as month"),
            DB::raw("DATE_FORMAT(created_at, '%Y') as year"),
            DB::raw('COUNT(*) as total')
        )
            ->whereRaw("DATE_FORMAT(created_at, '%Y') = ?", [Carbon::now()->year])
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $date = Carbon::create($item->year, $item->month, 1);
                return [
                    'month' => $date->format('M'),
                    'total' => $item->total,
                ];
            });

        $jobsByCategory = JobCategory::withCount('jobVacancies')
            ->get()
            ->map(function ($category) {
                return [
                    'label' => $category->name,
                    'count' => $category->job_vacancies_count,
                ];
            });

        $totalJobSeekers = User::where('role', 'job_seeker')->count();
        $pendingApplications = Application::where('status', 'pending')->count();
        $interviewsScheduled = \App\Models\Interview::where('status', 'scheduled')->count();

        return view('admin.dashboard.index', compact(
            'totalVacancies',
            'totalApplicants',
            'activeJobs',
            'closedJobs',
            'recentApplications',
            'applicationsPerMonth',
            'jobsByCategory',
            'totalJobSeekers',
            'pendingApplications',
            'interviewsScheduled'
        ));
    }

    public function getChartData()
    {
        $applicationsPerMonth = Application::select(
            DB::raw("DATE_FORMAT(created_at, '%m') as month"),
            DB::raw("DATE_FORMAT(created_at, '%Y') as year"),
            DB::raw('COUNT(*) as total')
        )
            ->whereRaw("DATE_FORMAT(created_at, '%Y') = ?", [Carbon::now()->year])
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $date = Carbon::create($item->year, $item->month, 1);
                return [
                    'month' => $date->format('M'),
                    'total' => $item->total,
                ];
            });

        $jobsByCategory = JobCategory::withCount('jobVacancies')
            ->get()
            ->map(function ($category) {
                return [
                    'label' => $category->name,
                    'count' => $category->job_vacancies_count,
                ];
            });

        return response()->json([
            'applicationsPerMonth' => $applicationsPerMonth,
            'jobsByCategory' => $jobsByCategory,
        ]);
    }
}
