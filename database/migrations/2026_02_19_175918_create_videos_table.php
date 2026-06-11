<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upload_id')->nullable()->constrained()->nullOnDelete();
            $table->string('caption');
            $table->text('topic')->nullable();
            $table->text('script')->nullable();
            $table->string('video_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->enum('learning_style', ['visual', 'auditory', 'reading', 'kinesthetic'])->nullable();
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->enum('status', ['queued', 'processing', 'completed', 'failed'])->default('queued');
            $table->string('ai_job_id')->nullable(); // ID from AI backend
            $table->json('ai_metadata')->nullable();
            $table->string('language', 5)->default('en');
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
