<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    protected $guarded = [];

    protected $casts = [
        'design_options' => 'array',
    ];

    protected $appends = ['fullname', 'jobtitle'];

    public function getFullnameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getJobtitleAttribute(): ?string
    {
        return $this->attributes['job_title'] ?? null;
    }

    protected $attributes = [
        'design_options' => '{
            "font_size": "8.5pt",
            "line_height": 1.4,
            "font_family": "Inter, sans-serif",
            "accent_color": "#4f46e5",
            "section_order": ["summary", "experience", "education", "skills", "languages", "certifications", "projects"],
            "margins": {"top": 20, "bottom": 20, "left": 25, "right": 25}
        }',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(Language::class)->orderBy('sort_order');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class)->orderBy('sort_order');
    }
}
