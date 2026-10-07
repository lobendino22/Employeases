<?php

use App\Models\JobVacancy;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('job-vacancies:archive-expired', function () {
    $archived = JobVacancy::archiveExpired();

    $this->info("Archived {$archived} expired job " . Str::plural('vacancy', $archived) . '.');
})->purpose('Archive job vacancies whose application deadline has passed');

// Run at 11:59 PM so vacancies due today are archived at the end of their deadline day.
Schedule::command('job-vacancies:archive-expired')->dailyAt('23:59');
