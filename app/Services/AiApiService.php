<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiApiService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.ai.url', 'http://localhost:8001');
        $this->apiKey  = config('services.ai.api_key', '');
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'X-AI-Key'     => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ])->timeout(60);
    }

    // ── Video generation ─────────────────────────────────────────────────────

    /**
     * Request asynchronous video generation.
     * Returns ['job_id' => '...', 'status' => 'queued'].
     */
    public function requestVideoGeneration(array $payload): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/generate-video", $payload);
            $data     = $response->json();
            Log::info('Video generation queued', ['job_id' => $data['job_id'] ?? null]);
            return $data;
        } catch (\Exception $e) {
            Log::error('AI video generation request failed', ['error' => $e->getMessage()]);
            return ['job_id' => 'mock_' . uniqid(), 'status' => 'queued'];
        }
    }

    // ── Quiz generation ──────────────────────────────────────────────────────

    /**
     * Generate MCQs from a video script.
     *
     * @param  array  $payload  Must include 'script', 'video_id', 'topic', 'language'.
     *                          Optionally 'k_slide_question_texts'.
     * @return array            List of question objects ready for GenerateQuizJob.
     */
    public function generateQuiz(array $payload): array
    {
        try {
            $response = $this->client()
                ->timeout(300)
                ->post("{$this->baseUrl}/api/generate-quiz", $payload);

            $questions = $response->json('questions', []);
            Log::info('Quiz generated', ['count' => count($questions)]);
            return $questions;
        } catch (\Exception $e) {
            Log::error('AI quiz generation failed', ['error' => $e->getMessage()]);
            return $this->getMockQuizQuestions();
        }
    }

    // ── VARK classification ──────────────────────────────────────────────────

    /**
     * Classify a student's VARK learning style.
     *
     * Falls back to local scoring whenever the AI service is unreachable,
     * returns a non-2xx status, or omits the required 'result' key.
     */
    public function classifyVark(array $answers): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/classify-vark", [
                'answers' => $answers,
            ]);

            // A 404 / 500 is not a PHP exception — we must check explicitly.
            if (! $response->successful()) {
                Log::warning('VARK AI returned non-success status — falling back to local scoring', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->scoreVarkLocally($answers);
            }

            $data = $response->json();

            // Guard against a response that is missing the 'result' key.
            if (! isset($data['result'])) {
                Log::warning('VARK AI response missing "result" key — falling back to local scoring', [
                    'data' => $data,
                ]);
                return $this->scoreVarkLocally($answers);
            }

            return $data;

        } catch (\Exception $e) {
            Log::warning('VARK AI unavailable — falling back to local scoring', [
                'error' => $e->getMessage(),
            ]);
            return $this->scoreVarkLocally($answers);
        }
    }

    // ── Misconception analysis ───────────────────────────────────────────────

    /**
     * Analyse wrong answers and return misconception strings.
     */
    public function analyzeMisconceptions(array $payload): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/analyze-misconceptions", $payload);
            return $response->json('misconceptions', []);
        } catch (\Exception $e) {
            Log::error('Misconception analysis failed', ['error' => $e->getMessage()]);
            return ['Unable to identify specific misconceptions at this time.'];
        }
    }

    // ── Adaptive lesson ───────────────────────────────────────────────────────

    /**
     * Request an adaptive remedial lesson video.
     */
    public function requestAdaptiveLesson(array $payload): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/generate-adaptive-lesson", $payload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Adaptive lesson request failed', ['error' => $e->getMessage()]);
            return ['job_id' => 'mock_adaptive_' . uniqid()];
        }
    }

    // ── Local VARK fallback ───────────────────────────────────────────────────

    private function scoreVarkLocally(array $answers): array
    {
        $scores = ['visual' => 0, 'auditory' => 0, 'reading' => 0, 'kinesthetic' => 0];

        // Default letter→dimension map matches the questionnaire's a/b/c/d options.
        $letterMap = ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'];
        $configMap = config('vark.answer_map', []);

        foreach ($answers as $qId => $answer) {
            // Try the config map first (allows per-question overrides), then the
            // default letter map.
            $dim = $configMap[$qId][$answer] ?? $letterMap[strtolower($answer)] ?? null;
            if ($dim && array_key_exists($dim, $scores)) {
                $scores[$dim]++;
            }
        }

        $result = array_keys($scores, max($scores))[0];
        return array_merge($scores, ['result' => $result]);
    }

    // ── Mock fallback ─────────────────────────────────────────────────────────

    private function getMockQuizQuestions(): array
    {
        return [
            [
                'question'       => 'What is the main concept covered in this lesson?',
                'options'        => ['Option A', 'Option B', 'Option C', 'Option D'],
                'correct_answer' => 'Option A',
                'explanation'    => 'This is the primary concept covered.',
            ],
        ];
    }
}