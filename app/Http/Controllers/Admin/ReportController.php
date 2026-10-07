<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $totalApplicants = Application::count();
        $totalVacancies = JobVacancy::count();
        $totalJobSeekers = User::where('role', 'job_seeker')->count();

        $applicationsByStatus = Application::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        $vacanciesByCategory = JobCategory::withCount('jobVacancies')
            ->get();

        $monthlyApplications = Application::select(
            DB::raw("DATE_FORMAT(created_at, '%m') as month"),
            DB::raw("DATE_FORMAT(created_at, '%Y') as year"),
            DB::raw('COUNT(*) as total')
        )
            ->whereRaw("DATE_FORMAT(created_at, '%Y') = ?", [Carbon::now()->year])
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        $recentApplications = Application::with(['user', 'jobVacancy'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'totalApplicants',
            'totalVacancies',
            'totalJobSeekers',
            'applicationsByStatus',
            'vacanciesByCategory',
            'monthlyApplications',
            'recentApplications'
        ));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $pdf = Pdf::loadView('admin.reports.pdf', $data);

        return $pdf->download('employease-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);
        // Simple CSV export as fallback since maatwebsite/excel may not be installed
        $filename = 'employease-report-' . Carbon::now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, ['Title', 'Category', 'Applicants', 'Status', 'Created']);

            foreach ($data['vacancies'] as $vacancy) {
                fputcsv($file, [
                    $vacancy->title,
                    $vacancy->category?->name ?? 'N/A',
                    $vacancy->applicants_count,
                    $vacancy->is_open ? 'Open' : 'Closed',
                    $vacancy->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(Request $request): array
    {
        $query = JobVacancy::with(['category', 'applications'])
            ->withCount('applications')
            ->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('category_id')) {
            $query->where('job_category_id', $request->category_id);
        }

        $vacancies = $query->get();

        $totalApplicants = Application::count();
        $totalVacancies = $vacancies->count();

        return compact('vacancies', 'totalApplicants', 'totalVacancies');
    }
}
