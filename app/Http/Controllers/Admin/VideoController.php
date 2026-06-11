<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->where('caption', 'like', "%{$request->search}%"))
            ->latest()->paginate(15);
        return view('admin.videos.index', compact('videos'));
    }

    public function show(Video $video)
    {
        $video->load(['user', 'quiz.questions', 'upload']);
        return view('admin.videos.show', compact('video'));
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('admin.videos.index')->with('success', 'Video removed.');
    }
}
