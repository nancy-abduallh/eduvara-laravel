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

    // ── Language detection ────────────────────────────────────────────────────

    /**
     * Detect the pipeline language from a payload by inspecting TEXT CONTENT
     * first (topic / caption / script), then falling back to the explicit
     * `language` field.
     *
     * This makes the pipeline language depend on what the user TYPED, not on
     * which page/form they used.
     *
     * Returns 'ar' or 'en'.
     */
    private function detectLanguage(array $payload): string
    {
        // Gather all user-supplied text fields
        $textParts = array_filter([
            $payload['topic']   ?? '',
            $payload['caption'] ?? '',
            $payload['script']  ?? '',
        ]);
        $combined = implode(' ', $textParts);

        if (mb_strlen(trim($combined)) > 0) {
            return $this->containsArabic($combined) ? 'ar' : 'en';
        }

        // No text content — fall back to the explicit language field
        return $this->isArabicLangCode($payload['language'] ?? null) ? 'ar' : 'en';
    }

    /**
     * Return true if $text contains a meaningful proportion of Arabic characters
     * (≥ 15 % of non-whitespace characters are in an Arabic Unicode block).
     */
    private function containsArabic(string $text): bool
    {
        $letters = preg_replace('/\s+/u', '', $text);
        $total   = mb_strlen($letters);
        if ($total === 0) {
            return false;
        }

        // Count Arabic-block characters
        $arabicCount = 0;
        $len = mb_strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $char     = mb_substr($letters, $i, 1);
            $codePoint = mb_ord($char, 'UTF-8');
            if (
                ($codePoint >= 0x0600 && $codePoint <= 0x06FF)   // Arabic
                || ($codePoint >= 0x0750 && $codePoint <= 0x077F) // Arabic Supplement
                || ($codePoint >= 0x08A0 && $codePoint <= 0x08FF) // Arabic Extended-A
                || ($codePoint >= 0xFB50 && $codePoint <= 0xFDFF) // Arabic Presentation Forms-A
                || ($codePoint >= 0xFE70 && $codePoint <= 0xFEFF) // Arabic Presentation Forms-B
            ) {
                $arabicCount++;
            }
        }

        return ($arabicCount / $total) >= 0.15;
    }

    /**
     * Returns true when the language code string indicates Arabic.
     * Accepts: 'ar', 'ar_SA', 'ar-EG', 'arabic', 'عربي', 'عربية'
     * (case-insensitive).
     */
    private function isArabicLangCode(?string $language): bool
    {
        if (empty($language)) {
            return false;
        }
        $lang = mb_strtolower(trim($language));

        if (str_starts_with($lang, 'ar_') || str_starts_with($lang, 'ar-')) {
            return true;
        }

        return in_array($lang, ['ar', 'arabic', 'عربي', 'عربية'], true);
    }

    // ── Video generation ─────────────────────────────────────────────────────

    /**
     * Request asynchronous video generation.
     *
     * The pipeline (Arabic vs English) is chosen by detecting the LANGUAGE OF
     * THE INPUT TEXT.  The UI page the user was on is irrelevant — if they
     * typed Arabic on the English dashboard they get the Arabic pipeline, and
     * vice-versa.
     *
     * Returns ['job_id' => '...', 'status' => 'queued', 'language' => 'en|ar'].
     */
    public function requestVideoGeneration(array $payload): array
    {
        $lang    = $this->detectLanguage($payload);
        $arabic  = ($lang === 'ar');

        // Normalise language in payload so the FastAPI side never has to guess
        $payload['language'] = $lang;

        // Both /api/generate-video and /api/ar/generate-video now do their own
        // content-based detection; we still use the matching endpoint as a hint
        // so logs on the Python side also reflect the right pipeline.
        $endpoint = $arabic
            ? "{$this->baseUrl}/api/ar/generate-video"
            : "{$this->baseUrl}/api/generate-video";

        Log::info('Video generation routing', [
            'detected_language' => $lang,
            'endpoint'          => $endpoint,
            'topic_preview'     => mb_substr($payload['topic'] ?? '', 0, 60),
        ]);

        try {
            $response = $this->client()->post($endpoint, $payload);
            $data     = $response->json();
            Log::info('Video generation queued', [
                'job_id'   => $data['job_id']  ?? null,
                'language' => $data['language'] ?? $lang,
            ]);
            return $data;
        } catch (\Exception $e) {
            Log::error('AI video generation request failed', ['error' => $e->getMessage()]);
            return [
                'job_id'   => 'mock_' . uniqid(),
                'status'   => 'queued',
                'language' => $lang,
            ];
        }
    }

    // ── Quiz generation ──────────────────────────────────────────────────────

    /**
     * Generate MCQs from a video script.
     *
     * Routes to the Arabic pipeline when the SCRIPT OR TOPIC TEXT is Arabic,
     * regardless of which page the request originated from.
     *
     * @param  array  $payload  Must include 'script', 'video_id', 'topic'.
     *                          Optionally 'k_slide_question_texts', 'num_questions'.
     * @return array            List of question objects ready for GenerateQuizJob.
     */
    public function generateQuiz(array $payload): array
    {
        $lang = $this->detectLanguage($payload);
        $payload['language'] = $lang;

        $endpoint = ($lang === 'ar')
            ? "{$this->baseUrl}/api/ar/generate-quiz"
            : "{$this->baseUrl}/api/generate-quiz";

        try {
            $response = $this->client()
                ->timeout(300)
                ->post($endpoint, $payload);

            $questions = $response->json('questions', []);
            Log::info('Quiz generated', [
                'count'    => count($questions),
                'language' => $lang,
            ]);
            return $questions;
        } catch (\Exception $e) {
            Log::error('AI quiz generation failed', ['error' => $e->getMessage()]);
            return ($lang === 'ar')
                ? $this->getMockArabicQuizQuestions()
                : $this->getMockQuizQuestions();
        }
    }

    // ── VARK classification ──────────────────────────────────────────────────

    /**
     * Classify a student's VARK learning style.
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
     * The AI service handles the language internally based on the content.
     */
    public function analyzeMisconceptions(array $payload): array
    {
        // Detect from the wrong-answer text if available
        $lang = $this->detectLanguage($payload);
        $payload['language'] = $lang;

        try {
            $response = $this->client()->post("{$this->baseUrl}/api/analyze-misconceptions", $payload);
            return $response->json('misconceptions', []);
        } catch (\Exception $e) {
            Log::error('Misconception analysis failed', ['error' => $e->getMessage()]);
            return ($lang === 'ar')
                ? ['تعذّر تحديد المفاهيم الخاطئة في الوقت الحالي.']
                : ['Unable to identify specific misconceptions at this time.'];
        }
    }

    // ── Adaptive lesson ───────────────────────────────────────────────────────

    /**
     * Request an adaptive remedial lesson video.
     * Routes to the Arabic pipeline when the MISCONCEPTIONS TEXT is Arabic.
     */
    public function requestAdaptiveLesson(array $payload): array
    {
        // For adaptive lessons, the misconceptions array is the content signal
        $misconceptions = $payload['misconceptions'] ?? [];
        $sampleText     = implode(' ', array_slice(array_map('strval', $misconceptions), 0, 5));

        $detectionPayload = array_merge($payload, ['topic' => $sampleText]);
        $lang = $this->detectLanguage($detectionPayload);
        $payload['language'] = $lang;

        $endpoint = ($lang === 'ar')
            ? "{$this->baseUrl}/api/ar/generate-adaptive-lesson"
            : "{$this->baseUrl}/api/generate-adaptive-lesson";

        try {
            $response = $this->client()->post($endpoint, $payload);
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Adaptive lesson request failed', ['error' => $e->getMessage()]);
            return [
                'job_id'   => 'mock_adaptive_' . uniqid(),
                'language' => $lang,
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

        // ── OpenRouter chat ──────────────────────────────────────────────────────

    /**
     * Send a chat message to OpenRouter and return the assistant's reply.
     *
     * @param  string  $systemPrompt   System instructions for this conversation.
     * @param  array   $history        Prior turns: [['role'=>'user','content'=>'…'], …]
     * @param  string  $userMessage    The new user message.
     * @param  string|null $model      Override the default chat model.
     * @return string                  The assistant's plain-text reply.
     */
    public function openRouterChat(
        string $systemPrompt,
        array  $history,
        string $userMessage,
        ?string $model = null
    ): string {
        $model ??= config('services.openrouter.chat_model',
                    env('OPENROUTER_CHAT_MODEL', 'mistralai/mistral-7b-instruct:free'));

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history,
            [['role' => 'user',   'content' => $userMessage]],
        );

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'HTTP-Referer'  => config('app.url', 'https://edugenie.app'),
                'X-Title'       => 'EduGenie',
                'Content-Type'  => 'application/json',
            ])
            ->timeout(60)
            ->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'    => $model,
                'messages' => $messages,
            ]);

            if (! $response->successful()) {
                Log::warning('OpenRouter chat non-success', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return $this->fallbackReply($userMessage);
            }

            return $response->json('choices.0.message.content')
                ?? $this->fallbackReply($userMessage);

        } catch (\Exception $e) {
            Log::error('OpenRouter chat failed', ['error' => $e->getMessage()]);
            return $this->fallbackReply($userMessage);
        }
    }

    private function fallbackReply(string $userMessage): string
    {
        return str_contains(mb_strtolower($userMessage), ['؟', 'ما', 'كيف', 'لماذا'])
            ? 'عذراً، لم أتمكن من معالجة طلبك في الوقت الحالي. حاول مجدداً.'
            : 'Sorry, I could not process your request right now. Please try again.';
    }
}