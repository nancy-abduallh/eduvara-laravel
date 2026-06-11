<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\AiApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600; // 1 hour

    public function __construct(public Video $video) {}

    public function handle(AiApiService $aiService): void
    {
        try {
            $this->video->update(['status' => 'processing']);

            $response = $aiService->requestVideoGeneration([
                'video_id'         => $this->video->id,
                'topic'            => $this->video->topic,
                'caption'          => $this->video->caption,
                'learning_style'   => $this->video->learning_style,
                'proficiency'      => $this->video->proficiency_level,
                'language'         => $this->video->language,
                'script'           => $this->video->script,
                'webhook_url'      => route('api.webhook.video'),
            ]);

            $this->video->update(['ai_job_id' => $response['job_id'] ?? null]);

            Log::info('Video generation requested', ['video_id' => $this->video->id]);
        } catch (\Exception $e) {
            $this->video->update(['status' => 'failed']);
            Log::error('Video generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->video->update(['status' => 'failed']);
        Log::error('GenerateVideoJob failed permanently', [
            'video_id' => $this->video->id,
            'error'    => $exception->getMessage(),
        ]);
    }
}
