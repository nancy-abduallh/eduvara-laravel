<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'video_id', 'role', 'content', 'metadata'];
    protected $casts = ['metadata' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function video() { return $this->belongsTo(Video::class); }
}
