<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\Video;
use App\Services\AiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct(private AiApiService $aiService) {}

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'video_id' => 'required|integer|exists:videos,id',
            'message'  => 'required|string|max:2000',
        ]);

        $video = Video::findOrFail($request->video_id);

        // Make sure the video belongs to the authenticated user
        if ($video->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userMessage = $request->message;

        // Load the last 10 turns (20 rows) for context
        $history = ChatHistory::where('user_id', auth()->id())
            ->where('video_id', $video->id)
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get(['role', 'content'])
            ->map(fn($m) => ['role' => $m->role, 'content' => $m->content])
            ->toArray();

        // Build the system prompt from the video's topic / caption / script
        $systemPrompt = $this->buildSystemPrompt($video);

        // Call OpenRouter
        $aiReply = $this->aiService->openRouterChat($systemPrompt, $history, $userMessage);

        // Persist both turns
        ChatHistory::create([
            'user_id'  => auth()->id(),
            'video_id' => $video->id,
            'role'     => 'user',
            'content'  => $userMessage,
        ]);

        ChatHistory::create([
            'user_id'  => auth()->id(),
            'video_id' => $video->id,
            'role'     => 'assistant',
            'content'  => $aiReply,
        ]);

        return response()->json(['reply' => $aiReply]);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function buildSystemPrompt(Video $video): string
    {
        $lines = [
            "You are an educational assistant helping a student understand a video lesson.",
            "Video title: {$video->caption}",
        ];

        if ($video->topic) {
            $lines[] = "Topic: {$video->topic}";
        }
        if ($video->script) {
            // Trim very long scripts so we don't blow the context window
            $script = mb_substr($video->script, 0, 3000);
            $lines[] = "Lesson script (excerpt):\n{$script}";
        }

        $lines[] = "Answer questions about this lesson clearly and concisely.";
        $lines[] = "If the student writes in Arabic, reply in Arabic. Otherwise reply in English.";

        return implode("\n", $lines);
    }
}