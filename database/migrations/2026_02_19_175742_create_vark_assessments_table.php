<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vark_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('answers'); // {question_id: answer}
            $table->integer('visual_score')->default(0);
            $table->integer('auditory_score')->default(0);
            $table->integer('reading_score')->default(0);
            $table->integer('kinesthetic_score')->default(0);
            $table->enum('result', ['visual', 'auditory', 'reading', 'kinesthetic'])->nullable();
            $table->string('ai_model_version')->nullable();
            $table->json('ai_raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vark_assessments');
    }
};
