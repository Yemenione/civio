<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feature',
        'model',
        'provider',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'cost_estimate',
        'response_time_ms',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
