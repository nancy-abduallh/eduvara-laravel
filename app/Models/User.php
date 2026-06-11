<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'role',
        'learning_style', 'proficiency_level',
        'onboarding_completed', 'language_preference',
        'avatar', 'last_active_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'onboarding_completed' => 'boolean',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function needsOnboarding(): bool
    {
        return !$this->onboarding_completed;
    }

    // Relationships
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function varkAssessments()
    {
        return $this->hasMany(VarkAssessment::class);
    }

    public function latestVark()
    {
        return $this->hasOne(VarkAssessment::class)->latestOfMany();
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function adaptiveLessons()
    {
        return $this->hasMany(AdaptiveLesson::class);
    }

    public function chatHistories()
    {
        return $this->hasMany(ChatHistory::class);
    }

    public function getLearningStyleBadgeAttribute(): string
    {
        return match($this->learning_style) {
            'visual' => '👁️ Visual',
            'auditory' => '👂 Auditory',
            'reading' => '📖 Reading/Writing',
            'kinesthetic' => '🤲 Kinesthetic',
            default => '❓ Not assessed',
        };
    }
}
