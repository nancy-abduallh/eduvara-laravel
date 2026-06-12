<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'upload_id', 'caption', 'topic', 'script',
        'video_path', 'thumbnail_path', 'duration_seconds',
        'learning_style', 'proficiency_level', 'status',
        'ai_job_id', 'ai_metadata', 'language', 'view_count',
    ];

    protected $casts = [
        'ai_metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function chatHistories()
    {
        return $this->hasMany(ChatHistory::class);
    }

    public function getVideoUrlAttribute(): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        
        $aiBase = rtrim(config('services.ai.url', 'http://localhost:8001'), '/');
        return $aiBase . '/outputs/' . ltrim($this->video_path, '/');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return asset('images/default-thumbnail.jpg');
        }

        $aiBase = rtrim(config('services.ai.url', 'http://localhost:8001'), '/');
        return $aiBase . '/outputs/' . ltrim($this->thumbnail_path, '/');
    }

    public function isReady(): bool
    {
        return $this->status === 'completed';
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
