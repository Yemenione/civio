<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'title',
        'description',
        'keywords',
        'og_image',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'keywords' => 'array',
    ];

    /**
     * Get translated attribute
     */
    public function translate($attribute, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->$attribute[$locale] ?? $this->$attribute['en'] ?? null;
    }
}
