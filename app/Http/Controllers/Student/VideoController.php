<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateVideoJob;
use App\Jobs\GenerateQuizJob;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos = auth()->user()->videos()->latest()->paginate(12);
        return view('student.videos.index', compact('videos'));
    }

    public function show(Video $video)
    {
        $this->authorize('view', $video);
        $video->increment('view_count');
        $quiz = $video->quiz()->with('questions')->first();
        $chatHistory = $video->chatHistories()
            ->where('user_id', auth()->id())
            ->orderBy('created_at')
            ->get();
        return view('student.videos.show', compact('video', 'quiz', 'chatHistory'));
    }

    public function requestGeneration(Request $request)
    {
        $request->validate([
            'topic'   => 'required|string|max:500',
            'caption' => 'required|string|max:255',
        ]);

        $user  = auth()->user();
        $video = Video::create([
            'user_id'          => $user->id,
            'caption'          => $request->caption,
            'topic'            => $request->topic,
            'learning_style'   => $user->learning_style,
            'proficiency_level'=> $user->proficiency_level ?? 'beginner',
            'language'         => $user->language_preference ?? 'en',
            'status'           => 'queued',
        ]);

        GenerateVideoJob::dispatch($video);

        return response()->json([
            'success'  => true,
            'video_id' => $video->id,
            'message'  => 'Video generation queued successfully!',
        ]);
    }

    public function status(Video $video)
    {
        $this->authorize('view', $video);
        return response()->json([
            'status'    => $video->status,
            'video_url' => $video->video_url,
            'ready'     => $video->isReady(),
        ]);
    }
}
