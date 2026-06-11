<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class HistoryController extends Controller
{
    public function index()
    {
        return view('student.history.index');
    }

    public function videos()
    {
        $videos = auth()->user()->videos()
            ->with('quiz')
            ->latest()
            ->paginate(12);
        return view('student.history.videos', compact('videos'));
    }

    public function chats()
    {
        $chats = auth()->user()->chatHistories()
            ->with('video')
            ->latest()
            ->paginate(20);
        return view('student.history.chats', compact('chats'));
    }
}
