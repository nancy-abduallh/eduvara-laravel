<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarkAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'answers', 'visual_score', 'auditory_score',
        'reading_score', 'kinesthetic_score', 'result',
        'ai_model_version', 'ai_raw_response',
    ];

    protected $casts = [
        'answers' => 'array',
        'ai_raw_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getScoresAttribute(): array
    {
        return [
            'visual' => $this->visual_score,
            'auditory' => $this->auditory_score,
            'reading' => $this->reading_score,
            'kinesthetic' => $this->kinesthetic_score,
        ];
    }
}
