<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateQuizJob;
use App\Models\AdaptiveLesson;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiWebhookController extends Controller
{
    /**
     * Called by the AI service when a video finishes generating.
     *
     * Payload shape:
     * {
     *   "job_id":             "...",
     *   "status":             "completed" | "failed",
     *   "video_path":         "relative/path/to/video.mp4",
     *   "thumbnail_path":     "relative/path/to/thumbnail.jpg",
     *   "script":             "full lecture script text...",
     *   "k_slide_questions":  [{ question, options, correct_answer, explanation }, ...]
     * }
     */
    public function videoComplete(Request $request)
    {
        // ── Verify webhook HMAC signature ─────────────────────────────────────
        $sig      = $request->header('X-Webhook-Signature');
        $expected = hash_hmac('sha256', $request->getContent(), config('services.ai.webhook_secret'));
        if (!hash_equals($expected, $sig ?? '')) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $video = Video::where('ai_job_id', $request->job_id)->first();
        if (!$video) {
            return response()->json(['error' => 'Video not found'], 404);
        }

        if ($request->status === 'completed') {
            // Persist video metadata
            $video->update([
                'status'          => 'completed',
                'video_path'      => $request->video_path,
                'thumbnail_path'  => $request->thumbnail_path,
                'duration_seconds'=> $request->duration ?? null,
                'script'          => $request->script ?? null,
                'ai_metadata'     => $request->metadata ?? null,
            ]);

            // Extract K-slide question texts so they can be excluded from the quiz
            $kSlideQuestions = $request->k_slide_questions ?? [];
            $kSlideTexts     = array_map(
                fn($q) => is_array($q) ? ($q['question'] ?? '') : '',
                $kSlideQuestions
            );
            $kSlideTexts = array_filter($kSlideTexts);  // remove empties

            // Queue quiz generation, passing K-slide questions to exclude
            GenerateQuizJob::dispatch($video, array_values($kSlideTexts));

        } else {
            $video->update(['status' => 'failed']);
        }

        Log::info('Video webhook received', [
            'job_id'     => $request->job_id,
            'status'     => $request->status,
            'k_excluded' => count($request->k_slide_questions ?? []),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Called when quiz generation completes (currently just acknowledged).
     */
    public function quizComplete(Request $request)
    {
        return response()->json(['success' => true]);
    }

    /**
     * Called when an adaptive lesson video finishes.
     */
    public function adaptiveComplete(Request $request)
    {
        $lesson = AdaptiveLesson::where('ai_job_id', $request->job_id)->first();
        if ($lesson) {
            $lesson->update([
                'status'        => $request->status === 'completed' ? 'completed' : 'failed',
                'video_path'    => $request->video_path ?? null,
                'lesson_script' => $request->script ?? null,
            ]);
        }
        return response()->json(['success' => true]);
    }
}