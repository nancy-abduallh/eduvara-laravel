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

    // ── Language helper ───────────────────────────────────────────────────────

    /**
     * Returns true when the language code indicates Arabic.
     * Accepts: 'ar', 'arabic', 'عربي', 'عربية' (case-insensitive).
     */
    private function isArabic(?string $language): bool
    {
        if (empty($language)) {
            return false;
        }
        return in_array(
            mb_strtolower(trim($language)),
            ['ar', 'arabic', 'عربي', 'عربية'],
            true
        );
    }

    // ── Video generation ─────────────────────────────────────────────────────

    /**
     * Request asynchronous video generation.
     *
     * Automatically routes to the Arabic pipeline when $payload['language']
     * is 'ar' (or any Arabic language alias). The AI service also supports
     * an explicit POST /api/ar/generate-video endpoint.
     *
     * Returns ['job_id' => '...', 'status' => 'queued', 'language' => 'en|ar'].
     */
    public function requestVideoGeneration(array $payload): array
    {
        $language = $payload['language'] ?? 'en';

        // Use the explicit Arabic endpoint for clarity, even though the
        // unified /api/generate-video also auto-routes on language='ar'.
        $endpoint = $this->isArabic($language)
            ? "{$this->baseUrl}/api/ar/generate-video"
            : "{$this->baseUrl}/api/generate-video";

        try {
            $response = $this->client()->post($endpoint, $payload);
            $data     = $response->json();
            Log::info('Video generation queued', [
                'job_id'   => $data['job_id']  ?? null,
                'language' => $data['language'] ?? $language,
            ]);
            return $data;
        } catch (\Exception $e) {
            Log::error('AI video generation request failed', ['error' => $e->getMessage()]);
            return [
                'job_id'   => 'mock_' . uniqid(),
                'status'   => 'queued',
                'language' => $language,
            ];
        }
    }

    // ── Quiz generation ──────────────────────────────────────────────────────

    /**
     * Generate MCQs from a video script.
     *
     * Routes to the Arabic pipeline when $payload['language'] is 'ar'.
     *
     * @param  array  $payload  Must include 'script', 'video_id', 'topic', 'language'.
     *                          Optionally 'k_slide_question_texts', 'num_questions'.
     * @return array            List of question objects ready for GenerateQuizJob.
     */
    public function generateQuiz(array $payload): array
    {
        $language = $payload['language'] ?? 'en';

        $endpoint = $this->isArabic($language)
            ? "{$this->baseUrl}/api/ar/generate-quiz"
            : "{$this->baseUrl}/api/generate-quiz";

        try {
            $response = $this->client()
                ->timeout(300)
                ->post($endpoint, $payload);

            $questions = $response->json('questions', []);
            Log::info('Quiz generated', [
                'count'    => count($questions),
                'language' => $language,
            ]);
            return $questions;
        } catch (\Exception $e) {
            Log::error('AI quiz generation failed', ['error' => $e->getMessage()]);
            return $this->isArabic($language)
                ? $this->getMockArabicQuizQuestions()
                : $this->getMockQuizQuestions();
        }
    }

    // ── VARK classification ──────────────────────────────────────────────────

    /**
     * Classify a student's VARK learning style.
     *
     * Falls back to local scoring whenever the AI service is unreachable,
     * returns a non-2xx status, or omits the required 'result' key.
     * (VARK classification is language-agnostic — same endpoint for both.)
     */
    public function classifyVark(array $answers): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/classify-vark", [
                'answers' => $answers,
            ]);

            if (! $response->successful()) {
                Log::warning('VARK AI returned non-success status — falling back to local scoring', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->scoreVarkLocally($answers);
            }

            $data = $response->json();

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
     * Works for both languages — the AI service handles the language internally.
     */
    public function analyzeMisconceptions(array $payload): array
    {
        try {
            $response = $this->client()->post("{$this->baseUrl}/api/analyze-misconceptions", $payload);
            return $response->json('misconceptions', []);
        } catch (\Exception $e) {
            Log::error('Misconception analysis failed', ['error' => $e->getMessage()]);

            $language = $payload['language'] ?? 'en';
            return $this->isArabic($language)
                ? ['تعذّر تحديد المفاهيم الخاطئة في الوقت الحالي.']
                : ['Unable to identify specific misconceptions at this time.'];
        }
    }

    // ── Adaptive lesson ───────────────────────────────────────────────────────

    /**
     * Request an adaptive remedial lesson video.
     * Routes to the Arabic pipeline when $payload['language'] is 'ar'.
     */
    public function requestAdaptiveLesson(array $payload): array
    {
        $language = $payload['language'] ?? 'en';

        $endpoint = $this->isArabic($language)
            ? "{$this->baseUrl}/api/ar/generate-adaptive-lesson"
            : "{$this->baseUrl}/api/generate-adaptive-lesson";

        try {
            $response = $this->client()->post($endpoint, $payload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Adaptive lesson request failed', ['error' => $e->getMessage()]);
            return [
                'job_id'   => 'mock_adaptive_' . uniqid(),
                'language' => $language,
            ];
        }
    }

    // ── Local VARK fallback ───────────────────────────────────────────────────

    private function scoreVarkLocally(array $answers): array
    {
        $scores = ['visual' => 0, 'auditory' => 0, 'reading' => 0, 'kinesthetic' => 0];

        $letterMap = ['a' => 'visual', 'b' => 'auditory', 'c' => 'reading', 'd' => 'kinesthetic'];
        $configMap = config('vark.answer_map', []);

        foreach ($answers as $qId => $answer) {
            $dim = $configMap[$qId][$answer] ?? $letterMap[strtolower($answer)] ?? null;
            if ($dim && array_key_exists($dim, $scores)) {
                $scores[$dim]++;
            }
        }

        $result = array_keys($scores, max($scores))[0];
        return array_merge($scores, ['result' => $result]);
    }

    // ── Mock fallbacks ────────────────────────────────────────────────────────

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

    private function getMockArabicQuizQuestions(): array
    {
        return [
            [
                'question'       => 'ما المفهوم الرئيسي الذي تناولته هذه المحاضرة؟',
                'options'        => ['الخيار أ', 'الخيار ب', 'الخيار ج', 'الخيار د'],
                'correct_answer' => 'الخيار أ',
                'explanation'    => 'هذا هو المفهوم الأساسي المُغطَّى في المحاضرة.',
            ],
        ];
    }
}