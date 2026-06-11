<?php

namespace App\Jobs;

use App\Models\Video;
use App\Models\Quiz;
use App\Services\AiApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateQuizJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 3;
    public $timeout = 600;

    public function __construct(
        public Video $video,
        /** @var array<string> Question texts already embedded as K-type slides */
        public array $kSlideQuestionTexts = [],
    ) {}

    public function handle(AiApiService $aiService): void
    {
        $quiz = Quiz::create([
            'video_id' => $this->video->id,
            'title'    => 'Quiz: ' . $this->video->caption,
            'status'   => 'generating',
        ]);

        $questions = $aiService->generateQuiz([
            'video_id'              => $this->video->id,
            'topic'                 => $this->video->topic,
            'script'                => $this->video->script,
            'language'              => $this->video->language,
            // Exclude questions already used in K-type slides
            'k_slide_question_texts' => $this->kSlideQuestionTexts,
        ]);

        foreach ($questions as $index => $q) {
            // The AI service returns options as an array [A, B, C, D]
            // Map to the format expected by the quiz model
            $options = $q['options'] ?? [];
            if (is_array($options) && !array_key_exists('A', $options)) {
                // Numeric array from quiz endpoint → convert to labelled
                $labelled = [];
                foreach (['A', 'B', 'C', 'D'] as $i => $letter) {
                    $labelled[$letter] = $options[$i] ?? '';
                }
                $options = $labelled;
            }

            $quiz->questions()->create([
                'question'       => $q['question'],
                'options'        => $options,
                'correct_answer' => $q['correct_answer'],
                'explanation'    => $q['explanation'] ?? null,
                'order'          => $index,
            ]);
        }

        $quiz->update(['status' => 'ready']);
        Log::info('Quiz generated', [
            'video_id'   => $this->video->id,
            'quiz_id'    => $quiz->id,
            'questions'  => count($questions),
            'k_excluded' => count($this->kSlideQuestionTexts),
        ]);
    }
}