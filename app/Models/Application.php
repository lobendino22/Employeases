<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_vacancy_id',
        'cover_letter',
        'resume_path',
        'status',
        'admin_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobVacancy(): BelongsTo
    {
        // Archived vacancies stay attached to past applications so their details remain visible.
        return $this->belongsTo(JobVacancy::class)->withTrashed();
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'under_review' => 'info',
            'shortlisted' => 'primary',
            'interview_scheduled' => 'purple',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public static function statuses(): array
    {
        return [
            'pending',
            'under_review',
            'shortlisted',
            'interview_scheduled',
            'accepted',
            'rejected',
        ];
    }
}
