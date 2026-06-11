@extends('layouts.student')
@section('title', 'Quiz: ' . $video->caption)
@section('page-title', 'Quiz')

@push('styles')
<style>
.quiz-wrap { max-width: 720px; margin: 0 auto; }
.quiz-header {
    background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(239,68,68,0.1));
    border: 1px solid rgba(245,158,11,0.25);
    border-radius: 20px; padding: 1.8rem; margin-bottom: 2rem;
}
.quiz-header h2 { font-size: 1.3rem; font-weight: 700; margin-bottom: 0.4rem; }
.quiz-header p  { color: var(--muted); font-size: 0.88rem; }
.quiz-progress-wrap { background: rgba(255,255,255,0.06); border-radius: 50px; height: 5px; margin: 1rem 0 0.4rem; overflow: hidden; }
.quiz-progress-fill { height: 100%; background: linear-gradient(90deg,#F59E0B,#EF4444); border-radius: 50px; transition: width 0.4s; }
.quiz-q-label { font-size: 0.75rem; color: var(--muted); text-align: right; }

.q-item {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 18px; padding: 2rem; margin-bottom: 1.5rem;
    display: none;
}
.q-item.active { display: block; animation: fadeIn 0.35s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(15px)} to{opacity:1;transform:none} }
.q-question { font-size: 1.05rem; font-weight: 600; margin-bottom: 1.5rem; line-height: 1.5; }
.q-number { font-size: 0.75rem; color: var(--secondary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.6rem; }
.options-list { display: flex; flex-direction: column; gap: 0.8rem; }
.opt-label {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.2rem; border-radius: 12px; cursor: pointer;
    background: rgba(255,255,255,0.03); border: 1px solid var(--border);
    transition: all 0.2s;
}
.opt-label:hover { border-color: rgba(245,158,11,0.4); background: rgba(245,158,11,0.08); }
.opt-label input[type="radio"] { display: none; }
.opt-label:has(input:checked) {
    border-color: #F59E0B; background: rgba(245,158,11,0.12);
    box-shadow: 0 0 0 1px rgba(245,158,11,0.4);
}
.opt-key {
    min-width: 32px; height: 32px; border-radius: 8px;
    background: rgba(245,158,11,0.15); color: #FCD34D;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.85rem;
}
.opt-text { font-size: 0.93rem; }

.quiz-nav { display: flex; gap: 1rem; justify-content: space-between; margin-top: 1.5rem; }
.btn-q-prev {
    background: var(--card); border: 1px solid var(--border);
    color: var(--muted); padding: 0.75rem 1.8rem;
    border-radius: 50px; cursor: pointer; font-weight: 600; font-size: 0.9rem;
    transition: all 0.2s;
}
.btn-q-prev:hover { border-color: var(--primary); color: var(--text); }
.btn-q-next {
    background: linear-gradient(135deg, #F59E0B, #EF4444);
    color: #fff; border: none; padding: 0.75rem 2rem;
    border-radius: 50px; cursor: pointer; font-weight: 700; font-size: 0.9rem;
    box-shadow: 0 0 20px rgba(245,158,11,0.3);
    transition: all 0.25s;
}
.btn-q-next:hover { box-shadow: 0 0 35px rgba(245,158,11,0.4); transform: translateY(-2px); }
.btn-q-next:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
.btn-submit-quiz {
    background: linear-gradient(135deg, #10B981, #059669);
    color: #fff; border: none; padding: 0.9rem 2.5rem;
    border-radius: 50px; cursor: pointer; font-weight: 700; font-size: 1rem;
    box-shadow: 0 0 25px rgba(16,185,129,0.35); transition: all 0.25s;
}
.btn-submit-quiz:hover { box-shadow: 0 0 40px rgba(16,185,129,0.5); transform: translateY(-2px); }
.btn-submit-quiz:disabled { opacity: 0.4; cursor: not-allowed; }
</style>
@endpush

@section('content')
<div class="quiz-wrap">
    <div class="quiz-header">
        <h2>📝 {{ $quiz->title }}</h2>
        <p>{{ $quiz->questions->count() }} AI-generated questions · Based on: <em>{{ Str::limit($video->caption, 50) }}</em></p>
        <div class="quiz-progress-wrap">
            <div class="quiz-progress-fill" id="quizProgress" style="width:{{ (1/$quiz->questions->count())*100 }}%"></div>
        </div>
        <div class="quiz-q-label" id="quizLabel">1 / {{ $quiz->questions->count() }}</div>
    </div>

    <form method="POST" action="{{ route('student.quiz.submit', $quiz) }}" id="quizForm">
        @csrf
        @foreach($quiz->questions as $index => $q)
        <div class="q-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
            <div class="q-number">Question {{ $index + 1 }}</div>
            <div class="q-question">{{ $q->question }}</div>
            <div class="options-list">
                @foreach($q->options as $key => $option)
                <label class="opt-label">
                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $key }}" required>
                    <span class="opt-key">{{ strtoupper(substr($key, 0, 1)) }}</span>
                    <span class="opt-text">{{ $option }}</span>
                </label>
                @endforeach
            </div>

            <div class="quiz-nav">
                @if($index > 0)
                    <button type="button" class="btn-q-prev" onclick="quizNav({{ $index - 1 }})">← Previous</button>
                @else
                    <span></span>
                @endif

                @if(!$loop->last)
                    <button type="button" class="btn-q-next" id="qnext-{{ $index }}" onclick="quizNav({{ $index + 1 }})" disabled>
                        Next →
                    </button>
                @else
                    <button type="submit" class="btn-submit-quiz" id="submitQuiz" disabled>
                        ✅ Submit Quiz
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
</div>
@endsection

@push('scripts')
<script>
let currentQ = 0;
const totalQ = {{ $quiz->questions->count() }};

function quizNav(idx) {
    document.querySelector('.q-item.active').classList.remove('active');
    document.querySelectorAll('.q-item')[idx].classList.add('active');
    currentQ = idx;
    const pct = ((idx + 1) / totalQ) * 100;
    document.getElementById('quizProgress').style.width = pct + '%';
    document.getElementById('quizLabel').textContent = (idx + 1) + ' / ' + totalQ;
}

document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const item = radio.closest('.q-item');
        const idx  = parseInt(item.dataset.index);
        const nextBtn   = document.getElementById('qnext-' + idx);
        const submitBtn = document.getElementById('submitQuiz');
        if (nextBtn)   nextBtn.disabled   = false;
        if (submitBtn && idx === totalQ - 1) submitBtn.disabled = false;
    });
});
</script>
@endpush
