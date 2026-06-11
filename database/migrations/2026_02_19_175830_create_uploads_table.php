<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->enum('file_type', ['text', 'pdf', 'pptx', 'voice', 'image']);
            $table->bigInteger('file_size')->nullable(); // bytes
            $table->text('extracted_text')->nullable();
            $table->json('preprocessed_data')->nullable(); // BERT output
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('processing_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
