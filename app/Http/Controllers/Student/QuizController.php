<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateAdaptiveLessonJob;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Video;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Video $video)
    {
        $quiz = $video->quiz()->with('questions')->firstOrFail();
        return view('student.quiz.show', compact('quiz', 'video'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate(['answers' => 'required|array']);

        $correct = 0;
        foreach ($quiz->questions as $q) {
            if (($request->answers[$q->id] ?? null) === $q->correct_answer) {
                $correct++;
            }
        }

        $total   = $quiz->questions->count();
        $score   = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed  = $score >= 70;

        $attempt = QuizAttempt::create([
            'user_id'         => auth()->id(),
            'quiz_id'         => $quiz->id,
            'answers'         => $request->answers,
            'score'           => $score,
            'total_questions' => $total,
            'correct_answers' => $correct,
            'passed'          => $passed,
        ]);

        if (!$passed) {
            GenerateAdaptiveLessonJob::dispatch($attempt);
        }

        return redirect()->route('student.quiz.result', $attempt);
    }

    public function result(QuizAttempt $attempt)
    {
        $this->authorize('view', $attempt);
        $attempt->load(['quiz.questions', 'adaptiveLesson']);
        return view('student.quiz.result', compact('attempt'));
    }
}
