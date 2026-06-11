<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'original_filename', 'stored_filename',
        'file_path', 'file_type', 'file_size', 'extracted_text',
        'preprocessed_data', 'status', 'processing_error',
    ];
    protected $casts = ['preprocessed_data' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
    public function videos() { return $this->hasMany(Video::class); }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
