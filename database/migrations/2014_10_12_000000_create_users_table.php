<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['student', 'admin'])->default('student');
            $table->enum('learning_style', ['visual', 'auditory', 'reading', 'kinesthetic'])->nullable();
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced'])->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->string('language_preference', 5)->default('en');
            $table->string('avatar')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
