<?php

namespace App\Http\Middleware;

use App\Models\JobVacancy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArchiveExpiredJobVacancies
{
    /**
     * Archive vacancies whose deadline lapsed as part of handling the request.
     *
     * The scheduler runs this at 11:59 PM, but it only fires when the server's
     * cron is configured. Running the same sweep on every request guarantees a
     * due vacancy reaches the archive as soon as anyone uses the app after the
     * deadline, instead of waiting for an admin to sign in.
     */
    public function handle(Request $request, Closure $next): Response
    {
        JobVacancy::archiveExpired();

        return $next($request);
    }
}
