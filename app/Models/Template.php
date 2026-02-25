<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'component',
        'description',
        'preview_image',
        'category',
        'is_premium',
        'is_active',
        'sort_order',
        'supported_languages',
    ];

    protected $casts = [
        'is_premium'          => 'boolean',
        'is_active'           => 'boolean',
        'supported_languages' => 'array',
    ];

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeFree($query)
    {
        return $query->active()->where('is_premium', false);
    }

    public function scopePremium($query)
    {
        return $query->active()->where('is_premium', true);
    }

    // ─── Helpers ──────────────────────────────────────────────

    /**
     * Returns true if this template supports RTL (Arabic).
     */
    public function supportsRtl(): bool
    {
        return $this->category === 'rtl'
            || (is_array($this->supported_languages) && in_array('ar', $this->supported_languages));
    }
}
