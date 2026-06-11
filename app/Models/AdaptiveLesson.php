<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdaptiveLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'quiz_attempt_id', 'misconceptions',
        'lesson_script', 'video_path', 'status', 'ai_job_id',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function quizAttempt() { return $this->belongsTo(QuizAttempt::class); }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }
}
