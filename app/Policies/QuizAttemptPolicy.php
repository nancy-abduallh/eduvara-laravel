<?php

namespace App\Policies;

use App\Models\User;
use App\Models\QuizAttempt;

class QuizAttemptPolicy
{
    public function view(User $user, QuizAttempt $attempt): bool
    {
        return $user->id === $attempt->user_id || $user->isAdmin();
    }
}
