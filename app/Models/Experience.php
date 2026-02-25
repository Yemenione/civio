<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    protected $guarded = [];

    protected $appends = ['jobtitle'];

    public function getJobtitleAttribute(): ?string
    {
        return $this->attributes['job_title'] ?? null;
    }


    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
