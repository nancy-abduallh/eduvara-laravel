@extends('layouts.app')
@section('title', __('messages.onboarding.result.title'))

@push('styles')
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root { --primary:#7C3AED;--secondary:#F59E0B;--accent:#10B981;--darker:#07070F;--text:#F8FAFC;--muted:rgba(248,250,252,.55);--border:rgba(255,255,255,.08);--glow:rgba(124,58,237,.35);--card:rgba(255,255,255,.04); }
body.eduvara-body { background:var(--darker);color:var(--text);font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center; }
.result-page { max-width:700px;width:100%;margin:0 auto;padding:3rem 2rem;text-align:center; }
.result-logo { font-family:'Space Grotesk',sans-serif;font-size:1.4rem;font-weight:700;background:linear-gradient(135deg,var(--primary),var(--secondary));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:3rem; }
.confetti-area { font-size:3.5rem;margin-bottom:1.5rem;animation:bounce 0.8s ease; }
@keyframes bounce { 0%,100%{transform:scale(1)} 50%{transform:scale(1.2)} }
.result-title { font-family:'Space Grotesk',sans-serif;font-size:2.2rem;font-weight:800;margin-bottom:0.5rem; }
.result-style {
    display:inline-block;
    background:linear-gradient(135deg,var(--primary),#9333EA);
    padding:0.4rem 1.5rem; border-radius:50px;
    font-size:1.1rem;font-weight:700;margin:1rem 0 1.5rem;
    box-shadow:0 0 30px var(--glow);
}
.result-desc { color:var(--muted);line-height:1.8;max-width:500px;margin:0 auto 2.5rem; }

/* Scores */
.scores-grid { display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:2.5rem; }
.score-item { background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.2rem; }
.score-name { font-size:0.8rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:0.5rem; }
.score-bar-bg { background:rgba(255,255,255,0.06);border-radius:50px;height:8px;overflow:hidden; }
.score-bar { height:100%;border-radius:50px;transition:width 1.2s cubic-bezier(.4,0,.2,1); }
.score-num { font-weight:700;font-size:1.1rem;margin-top:0.4rem; }

.btn-start { display:inline-flex;align-items:center;gap:0.6rem;background:linear-gradient(135deg,var(--primary),#9333EA);color:#fff;padding:1rem 2.5rem;border-radius:50px;text-decoration:none;font-weight:700;font-size:1rem;box-shadow:0 0 30px var(--glow);transition:all 0.25s; }
.btn-start:hover { transform:translateY(-3px);box-shadow:0 0 50px var(--glow); }

/* RTL Support */
[dir="rtl"] .scores-grid {
    direction: rtl;
}
</style>
@endpush

@section('content')
<div class="result-page">
    <div class="result-logo">⚡ EDUGENIE</div>
    <div class="confetti-area">🎉</div>
    <h1 class="result-title">{{ __('messages.onboarding.result.title') }}</h1>
    <div class="result-style">
        @php
            $icons = [
                'visual' => '👁️',
                'auditory' => '👂',
                'reading' => '📖',
                'kinesthetic' => '🤲'
            ];
        @endphp
        {{ $icons[$assessment->result] ?? '🧠' }} 
        @if($assessment->result == 'visual')
            {{ __('messages.onboarding.result.visual') }}
        @elseif($assessment->result == 'auditory')
            {{ __('messages.onboarding.result.auditory') }}
        @elseif($assessment->result == 'reading')
            {{ __('messages.onboarding.result.reading') }}
        @elseif($assessment->result == 'kinesthetic')
            {{ __('messages.onboarding.result.kinesthetic') }}
        @else
            {{ ucfirst($assessment->result) }}
        @endif
    </div>
    <p class="result-desc">
        @switch($assessment->result)
            @case('visual') {{ __('messages.onboarding.result.visual_desc') }} @break
            @case('auditory') {{ __('messages.onboarding.result.auditory_desc') }} @break
            @case('reading') {{ __('messages.onboarding.result.reading_desc') }} @break
            @case('kinesthetic') {{ __('messages.onboarding.result.kinesthetic_desc') }} @break
        @endswitch
    </p>

    <!-- Score Breakdown -->
    <div class="scores-grid">
        @php
            $max = max($assessment->visual_score, $assessment->auditory_score, $assessment->reading_score, $assessment->kinesthetic_score);
            $maxSafe = $max ?: 1;
            $colors = [
                'visual' => 'linear-gradient(90deg,#7C3AED,#9333EA)',
                'auditory' => 'linear-gradient(90deg,#F59E0B,#EF4444)',
                'reading' => 'linear-gradient(90deg,#10B981,#059669)',
                'kinesthetic' => 'linear-gradient(90deg,#EC4899,#A855F7)'
            ];
            $styleNames = [
                'visual' => __('messages.vark_styles.visual.name'),
                'auditory' => __('messages.vark_styles.auditory.name'),
                'reading' => __('messages.vark_styles.reading.name'),
                'kinesthetic' => __('messages.vark_styles.kinesthetic.name')
            ];
        @endphp
        @foreach(['visual','auditory','reading','kinesthetic'] as $style)
            @php $score = $assessment->{$style.'_score'}; $pct = round(($score/$maxSafe)*100); @endphp
            <div class="score-item">
                <div class="score-name">{{ $styleNames[$style] }}</div>
                <div class="score-bar-bg"><div class="score-bar" data-width="{{ $pct }}" style="background:{{ $colors[$style] }};width:0%"></div></div>
                <div class="score-num">{{ $score }} {{ __('messages.onboarding.result.points') }}</div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('student.dashboard') }}" class="btn-start">
        <i class="fas fa-rocket"></i> {{ __('messages.onboarding.result.continue') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
// Animate bars on load
window.addEventListener('load', () => {
    document.querySelectorAll('.score-bar').forEach(bar => {
        const w = bar.dataset.width;
        setTimeout(() => { bar.style.width = w + '%'; }, 300);
    });
});
</script>
@endpush