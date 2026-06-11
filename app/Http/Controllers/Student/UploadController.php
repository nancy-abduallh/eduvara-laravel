<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateVideoJob;
use App\Models\Upload;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = auth()->user()->uploads()->latest()->paginate(10);
        return view('student.upload', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file'    => 'required|file|max:51200|mimes:pdf,pptx,txt,doc,docx,mp3,wav,ogg',
            'caption' => 'required|string|max:255',
        ]);

        $file   = $request->file('file');
        $ext    = $file->getClientOriginalExtension();
        $stored = Str::uuid() . '.' . $ext;
        $path   = $file->storeAs('uploads/' . auth()->id(), $stored, 'public');

        $type = match(true) {
            in_array($ext, ['pdf'])            => 'pdf',
            in_array($ext, ['pptx'])           => 'pptx',
            in_array($ext, ['txt','doc','docx'])=> 'text',
            in_array($ext, ['mp3','wav','ogg']) => 'voice',
            default                             => 'text',
        };

        $upload = Upload::create([
            'user_id'           => auth()->id(),
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename'   => $stored,
            'file_path'         => $path,
            'file_type'         => $type,
            'file_size'         => $file->getSize(),
            'status'            => 'pending',
        ]);

        // Create video and dispatch generation
        $user  = auth()->user();
        $video = Video::create([
            'user_id'          => $user->id,
            'upload_id'        => $upload->id,
            'caption'          => $request->caption,
            'topic'            => $file->getClientOriginalName(),
            'learning_style'   => $user->learning_style,
            'proficiency_level'=> $user->proficiency_level ?? 'beginner',
            'language'         => $user->language_preference ?? 'en',
            'status'           => 'queued',
        ]);

        GenerateVideoJob::dispatch($video);
        $upload->update(['status' => 'processing']);

        return redirect()->route('student.videos.show', $video)
            ->with('success', 'File uploaded! Your video is being generated.');
    }
}
