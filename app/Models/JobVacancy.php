<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Archived vacancies are tracked in this column instead of the default "deleted_at".
     */
    const DELETED_AT = 'archived_at';

    protected $fillable = [
        'user_id',
        'job_category_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'employment_type',
        'location',
        'company',
        'slots_available',
        'application_deadline',
        'is_active',
        'is_open',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'slots_available' => 'integer',
            'application_deadline' => 'date',
            'is_active' => 'boolean',
            'is_open' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (JobVacancy $vacancy) {
            if (empty($vacancy->slug)) {
                $vacancy->slug = Str::slug($vacancy->title) . '-' . uniqid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('is_open', true);
    }

    /**
     * Only the archived (soft deleted) vacancies.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }

    public function scopeByEmploymentType($query, string $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('job_category_id', $categoryId);
    }

    public function scopeByLocation($query, string $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeBySalaryRange($query, $min, $max = null)
    {
        $query->where(function ($q) use ($min, $max) {
            $q->where('salary_max', '>=', $min);
            if ($max) {
                $q->where('salary_min', '<=', $max);
            }
        });
        return $query;
    }

    /**
     * Matches a free-text term against the vacancy itself and its category,
     * so searching "agriculture" returns every job in the Agriculture category
     * even when the word is not part of the title.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('location', 'like', "%{$term}%")
              ->orWhere('company', 'like', "%{$term}%")
              ->orWhereHas('category', function ($category) use ($term) {
                  $category->where('name', 'like', "%{$term}%");
              });
        });
    }

    public function getSalaryFormattedAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Negotiable';
        }
        $min = $this->salary_min ? '₱' . number_format($this->salary_min, 2) : '';
        $max = $this->salary_max ? '₱' . number_format($this->salary_max, 2) : '';
        return $min . ($min && $max ? ' - ' : '') . $max;
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->employment_type));
    }

    public function getApplicantsCountAttribute(): int
    {
        return $this->applications()->count();
    }

    public function isDeadlinePassed(): bool
    {
        return $this->hasExpiredDeadline();
    }

    /**
     * The moment the deadline lapses: 11:59 PM on the deadline day.
     */
    public function deadlineEndsAt(): ?\Illuminate\Support\Carbon
    {
        return $this->application_deadline?->copy()->setTime(23, 59);
    }

    /**
     * Whether the deadline has lapsed. The deadline day itself is still valid
     * and only counts as expired once 11:59 PM has been reached.
     */
    public function hasExpiredDeadline(): bool
    {
        $cutoff = $this->deadlineEndsAt();

        return $cutoff !== null && now()->gte($cutoff);
    }

    /**
     * Whole days left until the deadline, or null when there is no deadline.
     * Negative once the deadline has lapsed.
     */
    public function daysUntilDeadline(): ?int
    {
        if (!$this->application_deadline) {
            return null;
        }

        return (int) today()->startOfDay()->diffInDays(
            $this->application_deadline->copy()->startOfDay(),
            false
        );
    }

    /**
     * Whether the deadline falls within the next $days days (deadline day included).
     */
    public function isClosingSoon(int $days = 7): bool
    {
        $remaining = $this->daysUntilDeadline();

        return $remaining !== null && $remaining >= 0 && $remaining <= $days;
    }

    public function scopeClosingSoon(Builder $query, int $days = 7): Builder
    {
        return $query->whereNotNull('application_deadline')
            ->whereDate('application_deadline', '>=', today())
            ->whereDate('application_deadline', '<=', today()->addDays($days));
    }

    public function isArchived(): bool
    {
        return $this->trashed();
    }

    /**
     * Move every vacancy whose application deadline has lapsed into the archive.
     *
     * A deadline runs through the whole of its day and only lapses at 11:59 PM,
     * so vacancies due today are archived as soon as that minute is reached.
     *
     * Each vacancy is stamped with its own deadline end time — 11:59 PM on the
     * deadline date — rather than the moment the sweep happens to run.
     *
     * @return int number of vacancies archived
     */
    public static function archiveExpired(): int
    {
        $today = today();
        $deadlineDayEnded = now()->gte($today->copy()->setTime(23, 59));

        $expired = static::query()
            ->whereNotNull('application_deadline')
            ->where(function ($query) use ($today, $deadlineDayEnded) {
                $query->whereDate('application_deadline', '<', $today);

                if ($deadlineDayEnded) {
                    $query->orWhereDate('application_deadline', $today);
                }
            })
            ->get(['id', 'application_deadline']);

        if ($expired->isEmpty()) {
            return 0;
        }

        foreach ($expired as $vacancy) {
            if ($vacancy->application_deadline) {
                $archivedAt = $vacancy->application_deadline->copy()->setTime(23, 59);
                static::where('id', $vacancy->id)->update(['archived_at' => $archivedAt]);
            }
        }

        return $expired->count();
    }
}
