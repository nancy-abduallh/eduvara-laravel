@extends('layouts.student')
@section('title', 'Quiz Result')
@section('page-title', 'Quiz Result')

@push('styles')
<style>
.result-wrap { max-width: 720px; margin: 0 auto; }
.score-hero {
    text-align: center;
    background: var(--card); border: 1px solid var(--border);
    border-radius: 24px; padding: 3rem 2rem; margin-bottom: 2rem;
    position: relative; overflow: hidden;
}
.score-hero::before {
    content: ''; position: absolute; inset: 0;
    background: {{ $attempt->passed ? 'linear-gradient(135deg,rgba(16,185,129,0.1),rgba(5,150,105,0.05))' : 'linear-gradient(135deg,rgba(239,68,68,0.1),rgba(185,28,28,0.05))' }};
}
.score-circle {
    width: 150px; height: 150px; margin: 0 auto 1.5rem;
    border-radius: 50%; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    background: {{ $attempt->passed ? 'linear-gradient(135deg,#10B981,#059669)' : 'linear-gradient(135deg,#EF4444,#B91C1C)' }};
    box-shadow: 0 0 40px {{ $attempt->passed ? 'rgba(16,185,129,0.4)' : 'rgba(239,68,68,0.4)' }};
    position: relative;
}
.score-num { font-family:'Space Grotesk',sans-serif;font-size:2.5rem;font-weight:800;color:#fff; }
.score-pct { font-size:0.85rem;color:rgba(255,255,255,0.8); }
.result-badge {
    display:inline-block;padding:0.4rem 1.5rem;border-radius:50px;
    font-weight:700;font-size:0.9rem;margin-bottom:1rem;
    background:{{ $attempt->passed ? 'rgba(16,185,129,0.2)' : 'rgba(239,68,68,0.2)' }};
    color:{{ $attempt->passed ? '#34D399' : '#FCA5A5' }};
    border:1px solid {{ $attempt->passed ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)' }};
}
.result-title { font-size:1.5rem;font-weight:700;margin-bottom:0.5rem; }
.result-sub   { color:var(--muted);font-size:0.9rem; }

/* Answers review */
.answers-panel { background:var(--card);border:1px solid var(--border);border-radius:20px;padding:1.5rem;margin-bottom:1.5rem; }
.answers-panel h3 { font-size:1rem;font-weight:700;margin-bottom:1.2rem; }
.answer-item { padding:1rem;border-radius:12px;margin-bottom:0.8rem;border:1px solid; }
.answer-item.correct { background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25); }
.answer-item.wrong   { background:rgba(239,68,68,0.08);border-color:rgba(239,68,68,0.25); }
.answer-q  { font-weight:600;font-size:0.9rem;margin-bottom:0.6rem; }
.answer-meta { font-size:0.82rem;color:var(--muted);display:flex;flex-direction:column;gap:0.3rem; }
.correct-ans { color:#34D399; }
.wrong-ans   { color:#FCA5A5; }
.explanation { margin-top:0.5rem;font-size:0.82rem;color:var(--muted);font-style:italic; }

/* Adaptive Lesson */
.adaptive-panel {
    background:linear-gradient(135deg,rgba(124,58,237,0.15),rgba(245,158,11,0.08));
    border:1px solid rgba(124,58,237,0.3);border-radius:20px;padding:1.8rem;margin-bottom:1.5rem;
}

.action-row { display:flex;gap:1rem;flex-wrap:wrap; }
.btn-back {
    flex:1;display:block;text-align:center;
    background:var(--card);border:1px solid var(--border);color:var(--text);
    padding:0.85rem;border-radius:12px;text-decoration:none;font-weight:600;
    transition:all 0.2s;
}
.btn-back:hover { border-color:var(--primary); }
.btn-dashboard {
    flex:1;display:block;text-align:center;
    background:linear-gradient(135deg,#7C3AED,#9333EA);color:#fff;
    padding:0.85rem;border-radius:12px;text-decoration:none;font-weight:700;
    box-shadow:0 0 20px var(--glow);transition:all 0.25s;
}
.btn-dashboard:hover { transform:translateY(-2px);box-shadow:0 0 35px var(--glow); }
</style>
@endpush

@section('content')
<div class="result-wrap">
    <!-- Score Hero -->
    <div class="score-hero">
        <div class="score-circle">
            <div class="score-num">{{ $attempt->score }}</div>
            <div class="score-pct">%</div>
        </div>
        <div class="result-badge">{{ $attempt->passed ? '🏆 Passed' : '📚 Keep Going' }}</div>
        <h2 class="result-title">{{ $attempt->passed ? 'Excellent Work!' : 'Room to Grow!' }}</h2>
        <p class="result-sub">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }} correct answers</p>
    </div>

    <!-- Adaptive Lesson Banner -->
    @if(!$attempt->passed)
    <div class="adaptive-panel">
        <h3 style="margin-bottom:0.6rem;">🔄 Adaptive Lesson Generated</h3>
        <p style="color:var(--muted);font-size:0.88rem;margin-bottom:1rem;">
            Our AI has identified your knowledge gaps and is generating a personalized corrective micro-lesson for you.
        </p>
        @if($attempt->adaptiveLesson)
            @if($attempt->adaptiveLesson->status === 'completed' && $attempt->adaptiveLesson->video_path)
                <a href="{{ $attempt->adaptiveLesson->video_url }}" class="btn-quiz" style="display:inline-block;background:linear-gradient(135deg,#7C3AED,#9333EA);color:#fff;padding:0.75rem 2rem;border-radius:50px;text-decoration:none;font-weight:700;">
                    ▶ Watch Adaptive Lesson
                </a>
            @else
                <div style="display:flex;align-items:center;gap:0.8rem;color:#A78BFA;">
                    <div style="width:12px;height:12px;border:2px solid #7C3AED;border-top-color:transparent;border-radius:50%;animation:spin 1s linear infinite;"></div>
                    AI is generating your corrective lesson...
                </div>
            @endif
        @endif
    </div>
    @endif

    <!-- Answers Review -->
    <div class="answers-panel">
        <h3>📋 Answer Review</h3>
        @foreach($attempt->quiz->questions as $q)
            @php
                $userAnswer   = $attempt->answers[$q->id] ?? null;
                $isCorrect    = $userAnswer === $q->correct_answer;
                $userOption   = $q->options[$userAnswer] ?? 'Not answered';
                $correctOption= $q->options[$q->correct_answer] ?? $q->correct_answer;
            @endphp
            <div class="answer-item {{ $isCorrect ? 'correct' : 'wrong' }}">
                <div class="answer-q">{{ $loop->iteration }}. {{ $q->question }}</div>
                <div class="answer-meta">
                    @if(!$isCorrect)
                        <span class="wrong-ans">✗ Your answer: {{ $userOption }}</span>
                        <span class="correct-ans">✓ Correct: {{ $correctOption }}</span>
                    @else
                        <span class="correct-ans">✓ {{ $correctOption }}</span>
                    @endif
                    @if($q->explanation)
                        <span class="explanation">💡 {{ $q->explanation }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="action-row">
        <a href="{{ route('student.videos.show', $attempt->quiz->video_id) }}" class="btn-back">← Back to Video</a>
        <a href="{{ route('student.dashboard') }}" class="btn-dashboard">🏠 Dashboard</a>
    </div>
</div>
@endsection
