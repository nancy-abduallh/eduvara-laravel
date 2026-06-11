<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $videos  = $user->videos()->latest()->take(6)->get();
        $recent  = $user->quizAttempts()->with('quiz.video')->latest()->take(5)->get();
        $pending = $user->videos()->whereIn('status', ['queued', 'processing'])->count();
        $stats   = [
            'total_videos'  => $user->videos()->count(),
            'completed'     => $user->videos()->where('status', 'completed')->count(),
            'quiz_taken'    => $user->quizAttempts()->count(),
            'avg_score'     => $user->quizAttempts()->avg('score') ?? 0,
        ];

        return view('student.dashboard', compact('user', 'videos', 'recent', 'pending', 'stats'));
    }
}
