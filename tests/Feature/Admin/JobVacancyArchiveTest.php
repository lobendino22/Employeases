<?php

namespace Tests\Feature\Admin;

use App\Models\Application;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class JobVacancyArchiveTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private JobCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->category = JobCategory::create([
            'name' => 'Administrative',
            'is_active' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function vacancy(array $attributes = []): JobVacancy
    {
        return JobVacancy::create(array_merge([
            'user_id' => $this->admin->id,
            'job_category_id' => $this->category->id,
            'title' => 'Administrative Aide',
            'slug' => 'administrative-aide-' . uniqid(),
            'description' => 'Answer inquiries and file documents.',
            'employment_type' => 'full_time',
            'location' => 'Tagudin, Ilocos Sur',
            'slots_available' => 1,
            'application_deadline' => now()->addWeek()->toDateString(),
            'is_active' => true,
            'is_open' => true,
        ], $attributes));
    }

    private function isArchived(JobVacancy $vacancy): bool
    {
        return JobVacancy::withTrashed()->find($vacancy->id)->trashed();
    }

    public function test_vacancies_past_their_deadline_are_archived_automatically(): void
    {
        $expired = $this->vacancy(['application_deadline' => now()->subDay()->toDateString()]);
        $current = $this->vacancy(['application_deadline' => now()->addWeek()->toDateString()]);
        $withoutDeadline = $this->vacancy(['application_deadline' => null]);

        $this->actingAs($this->admin)
            ->get(route('admin.job-vacancies.index'))
            ->assertOk();

        $this->assertTrue($this->isArchived($expired));
        $this->assertFalse($this->isArchived($current));
        $this->assertFalse($this->isArchived($withoutDeadline));
    }

    public function test_archive_expired_command_archives_past_due_vacancies(): void
    {
        $expired = $this->vacancy(['application_deadline' => now()->subDays(3)->toDateString()]);
        $current = $this->vacancy();

        $this->artisan('job-vacancies:archive-expired')->assertSuccessful();

        $this->assertTrue($this->isArchived($expired));
        $this->assertFalse($this->isArchived($current));
    }

    public function test_vacancies_due_today_stay_active_before_1159_pm(): void
    {
        Carbon::setTestNow(today()->setTime(12, 0));

        $dueToday = $this->vacancy(['application_deadline' => today()->toDateString()]);

        $this->assertSame(0, JobVacancy::archiveExpired());
        $this->assertFalse($this->isArchived($dueToday));
    }

    public function test_vacancies_due_today_are_archived_at_1159_pm(): void
    {
        Carbon::setTestNow(today()->setTime(23, 59, 0));

        $dueToday = $this->vacancy(['application_deadline' => today()->toDateString()]);

        $this->assertSame(1, JobVacancy::archiveExpired());
        $this->assertTrue($this->isArchived($dueToday));
    }

    public function test_expired_vacancies_are_archived_on_any_request_without_an_admin_sign_in(): void
    {
        Carbon::setTestNow(today()->setTime(23, 59, 30));

        $dueToday = $this->vacancy(['application_deadline' => today()->toDateString()]);

        // A guest hitting the public homepage triggers the archive sweep.
        $this->get('/')->assertOk();

        $this->assertTrue($this->isArchived($dueToday));
    }

    public function test_admin_can_filter_vacancies_closing_soon(): void
    {
        $this->vacancy([
            'title' => 'Closing Soon Role',
            'application_deadline' => today()->addDays(3)->toDateString(),
        ]);
        $this->vacancy([
            'title' => 'Later Role',
            'application_deadline' => today()->addDays(30)->toDateString(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.job-vacancies.index', ['deadline' => 'closing_soon']))
            ->assertOk()
            ->assertSee('Closing Soon Role')
            ->assertSee('3 days left')
            ->assertDontSee('Later Role');
    }

    public function test_vacancies_due_in_the_future_are_not_archived(): void
    {
        Carbon::setTestNow(today()->setTime(23, 59, 0));

        $dueTomorrow = $this->vacancy(['application_deadline' => today()->addDay()->toDateString()]);

        $this->assertSame(0, JobVacancy::archiveExpired());
        $this->assertFalse($this->isArchived($dueTomorrow));
    }

    public function test_admin_can_view_archived_vacancies(): void
    {
        $archived = $this->vacancy(['title' => 'Archived Clerk Position']);
        $archived->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.job-vacancies.archived'))
            ->assertOk()
            ->assertSee('Archived Clerk Position');
    }

    public function test_archived_vacancies_are_hidden_from_the_active_listing(): void
    {
        $archived = $this->vacancy(['title' => 'Archived Clerk Position']);
        $archived->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.job-vacancies.index'))
            ->assertOk()
            ->assertDontSee('Archived Clerk Position');
    }

    public function test_deleting_a_vacancy_archives_it_instead_of_removing_it(): void
    {
        $vacancy = $this->vacancy();

        $this->actingAs($this->admin)
            ->delete(route('admin.job-vacancies.destroy', $vacancy))
            ->assertRedirect(route('admin.job-vacancies.index'));

        $this->assertTrue($this->isArchived($vacancy));
    }

    public function test_admin_can_restore_an_archived_vacancy(): void
    {
        $vacancy = $this->vacancy(['application_deadline' => now()->addMonth()->toDateString()]);
        $vacancy->delete();

        $this->actingAs($this->admin)
            ->post(route('admin.job-vacancies.restore', $vacancy->id))
            ->assertRedirect(route('admin.job-vacancies.archived'));

        $this->assertFalse($this->isArchived($vacancy));
    }

    public function test_restoring_an_expired_vacancy_prompts_for_a_new_deadline(): void
    {
        $vacancy = $this->vacancy(['application_deadline' => now()->subDay()->toDateString()]);
        $vacancy->delete();

        $this->actingAs($this->admin)
            ->post(route('admin.job-vacancies.restore', $vacancy->id))
            ->assertRedirect(route('admin.job-vacancies.edit', $vacancy))
            ->assertSessionHas('info');

        $this->assertFalse($this->isArchived($vacancy));
    }

    public function test_admin_can_permanently_delete_an_archived_vacancy(): void
    {
        $vacancy = $this->vacancy();
        $vacancy->delete();

        $this->actingAs($this->admin)
            ->delete(route('admin.job-vacancies.force-delete', $vacancy->id))
            ->assertRedirect(route('admin.job-vacancies.archived'));

        $this->assertDatabaseMissing('job_vacancies', ['id' => $vacancy->id]);
    }

    public function test_archived_vacancy_details_stay_viewable_and_flagged(): void
    {
        $vacancy = $this->vacancy();
        $vacancy->delete();

        $this->actingAs($this->admin)
            ->get(route('admin.job-vacancies.show', $vacancy))
            ->assertOk()
            ->assertSee('This vacancy is archived');
    }

    public function test_applications_keep_the_archived_vacancy_details(): void
    {
        $seeker = User::factory()->create([
            'role' => 'job_seeker',
            'email_verified_at' => now(),
        ]);

        $vacancy = $this->vacancy(['title' => 'Archived Clerk Position']);
        Application::create([
            'user_id' => $seeker->id,
            'job_vacancy_id' => $vacancy->id,
            'status' => 'pending',
        ]);

        $vacancy->delete();

        $this->actingAs($seeker)
            ->get(route('jobseeker.applications.index'))
            ->assertOk()
            ->assertSee('Archived Clerk Position');
    }

    public function test_job_seekers_cannot_access_the_archive(): void
    {
        $seeker = User::factory()->create([
            'role' => 'job_seeker',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($seeker)
            ->get(route('admin.job-vacancies.archived'))
            ->assertForbidden();
    }
}
