<?php

namespace App\Jobs;

use App\Models\AdaptiveLesson;
use App\Models\QuizAttempt;
use App\Services\AiApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAdaptiveLessonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    public function __construct(public QuizAttempt $attempt) {}

    public function handle(AiApiService $aiService): void
    {
        $lesson = AdaptiveLesson::create([
            'user_id'         => $this->attempt->user_id,
            'quiz_attempt_id' => $this->attempt->id,
            'status'          => 'queued',
        ]);

        $misconceptions = $aiService->analyzeMisconceptions([
            'attempt_id' => $this->attempt->id,
            'answers'    => $this->attempt->answers,
            'quiz_id'    => $this->attempt->quiz_id,
        ]);

        $lesson->update([
            'misconceptions' => implode(', ', $misconceptions),
            'status'         => 'processing',
        ]);

        $response = $aiService->requestAdaptiveLesson([
            'lesson_id'      => $lesson->id,
            'misconceptions' => $misconceptions,
            'user_id'        => $this->attempt->user_id,
            'webhook_url'    => route('api.webhook.video'),
        ]);

        $lesson->update(['ai_job_id' => $response['job_id'] ?? null]);
    }
}
