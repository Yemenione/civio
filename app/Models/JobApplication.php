<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $guarded = [];

    protected $casts = [
        'applied_at'   => 'date',
        'interview_at' => 'date',
        'salary_min'   => 'decimal:2',
        'salary_max'   => 'decimal:2',
    ];

    // Status pipeline
    const STATUSES = ['saved', 'applied', 'interview', 'offer', 'rejected', 'withdrawn'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
