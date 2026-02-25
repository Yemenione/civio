<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $guarded = [];

    protected $appends = ['name'];

    public function getNameAttribute(): ?string
    {
        return $this->title;
    }


    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
