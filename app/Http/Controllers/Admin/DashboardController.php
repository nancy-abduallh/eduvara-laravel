<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\QuizAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::where('role', 'student')->count(),
            'total_videos'   => Video::count(),
            'processing'     => Video::whereIn('status', ['queued','processing'])->count(),
            'completed'      => Video::where('status', 'completed')->count(),
            'failed'         => Video::where('status', 'failed')->count(),
            'quiz_attempts'  => QuizAttempt::count(),
            'avg_score'      => QuizAttempt::avg('score') ?? 0,
            'new_users_today'=> User::whereDate('created_at', today())->count(),
        ];

        $recentUsers  = User::where('role', 'student')->latest()->take(5)->get();
        $recentVideos = Video::with('user')->latest()->take(5)->get();

        $styleDistribution = User::where('role', 'student')
            ->whereNotNull('learning_style')
            ->selectRaw('learning_style, COUNT(*) as count')
            ->groupBy('learning_style')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentVideos', 'styleDistribution'));
    }

    public function system()
    {
        return view('admin.system');
    }
}
