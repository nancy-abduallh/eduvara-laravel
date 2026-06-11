@extends('layouts.app')
@section('title', __('messages.onboarding.vark.title'))

@push('styles')
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --primary: #7C3AED; --secondary: #F59E0B; --accent: #10B981;
    --dark: #0D0D1A; --darker: #07070F; --text: #F8FAFC;
    --muted: rgba(248,250,252,0.55); --border: rgba(255,255,255,0.08);
    --glow: rgba(124,58,237,0.35); --card: rgba(255,255,255,0.04);
}
body.eduvara-body {
    background: var(--darker); color: var(--text);
    font-family: 'Inter', sans-serif; min-height: 100vh;
}

/* RTL Support */
[dir="rtl"] {
    text-align: right;
}
[dir="rtl"] .option-label:hover {
    transform: translateX(-4px);
}
[dir="rtl"] .progress-label {
    text-align: left;
}
[dir="rtl"] .q-nav {
    flex-direction: row-reverse;
}
[dir="rtl"] .btn-prev, [dir="rtl"] .btn-next, [dir="rtl"] .btn-submit {
    flex-direction: row-reverse;
}
[dir="rtl"] .option-label {
    flex-direction: row-reverse;
}
[dir="rtl"] .option-key {
    margin-right: auto;
    margin-left: 0;
}

.onboard-wrap {
    max-width: 820px; margin: 0 auto; padding: 3rem 2rem 6rem;
}
.onboard-header { text-align: center; padding: 3rem 0 2rem; }
.onboard-logo {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.4rem; font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    margin-bottom: 2rem;
}
.onboard-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 2rem; font-weight: 700; margin-bottom: 0.8rem;
}
.onboard-sub { color: var(--muted); line-height: 1.7; max-width: 500px; margin: 0 auto; }

/* Progress */
.progress-bar-wrap {
    background: rgba(255,255,255,0.06);
    border-radius: 50px; height: 6px; margin: 2rem 0;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%; border-radius: 50px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    transition: width 0.4s ease;
}
.progress-label { text-align: right; font-size: 0.8rem; color: var(--muted); margin-top: 0.4rem; }

/* Question card */
.question-slide {
    display: none; animation: fadeSlide 0.4s ease;
}
.question-slide.active { display: block; }
@keyframes fadeSlide {
    from { opacity: 0; transform: translateX(30px); }
    to   { opacity: 1; transform: none; }
}
[dir="rtl"] @keyframes fadeSlide {
    from { opacity: 0; transform: translateX(-30px); }
    to   { opacity: 1; transform: none; }
}
.q-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px; padding: 2.5rem;
    backdrop-filter: blur(10px);
}
.q-num {
    font-size: 0.8rem; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
    color: var(--secondary); margin-bottom: 0.8rem;
}
.q-text { font-size: 1.15rem; font-weight: 600; margin-bottom: 1.8rem; line-height: 1.5; }
.options-grid { display: grid; gap: 0.8rem; }
.option-label {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.4rem;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
    border-radius: 12px; cursor: pointer;
    transition: border-color 0.2s, background 0.2s, transform 0.2s;
}
.option-label:hover {
    border-color: var(--primary);
    background: rgba(124,58,237,0.1);
    transform: translateX(4px);
}
[dir="rtl"] .option-label:hover {
    transform: translateX(-4px);
}
.option-label input[type="radio"] { display: none; }
.option-label input:checked ~ .option-text { color: #A78BFA; }
.option-label:has(input:checked) {
    border-color: var(--primary);
    background: rgba(124,58,237,0.15);
    box-shadow: 0 0 0 1px var(--primary);
}
.option-key {
    width: 32px; height: 32px; border-radius: 8px;
    background: rgba(124,58,237,0.15);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.85rem; flex-shrink: 0;
    color: #A78BFA;
}
.option-text { font-size: 0.95rem; }

/* Nav buttons */
.q-nav {
    display: flex; gap: 1rem; justify-content: space-between;
    margin-top: 1.5rem;
}
.btn-prev, .btn-next {
    padding: 0.75rem 2rem; border: none; border-radius: 50px;
    font-weight: 600; font-size: 0.9rem; cursor: pointer;
    transition: all 0.25s;
}
.btn-prev {
    background: var(--card); color: var(--muted);
    border: 1px solid var(--border);
}
.btn-prev:hover { border-color: var(--primary); color: var(--text); }
.btn-next {
    background: linear-gradient(135deg, var(--primary), #9333EA);
    color: #fff;
    box-shadow: 0 0 20px var(--glow);
}
.btn-next:hover { box-shadow: 0 0 35px var(--glow); transform: translateY(-2px); }
.btn-next:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
.btn-submit {
    background: linear-gradient(135deg, var(--accent), #059669);
    color: #fff; padding: 0.85rem 2.5rem;
    border: none; border-radius: 50px;
    font-weight: 700; font-size: 1rem; cursor: pointer;
    box-shadow: 0 0 25px rgba(16,185,129,0.4);
    transition: all 0.25s;
}
.btn-submit:hover { box-shadow: 0 0 40px rgba(16,185,129,0.5); transform: translateY(-2px); }
.btn-submit:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }
</style>
@endpush

@section('content')
<div class="onboard-wrap" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="onboard-header">
        <div class="onboard-logo">⚡ EDUGENIE</div>
        <h1 class="onboard-title">{{ __('messages.onboarding.vark.title') }}</h1>
        <p class="onboard-sub">{{ __('messages.onboarding.vark.subtitle') }}</p>
    </div>

    <div class="progress-bar-wrap">
        <div class="progress-bar-fill" id="progressFill" style="width:12.5%"></div>
    </div>
    <div class="progress-label"><span id="progressText">1 / {{ count($questions) }}</span></div>

    <form method="POST" action="{{ route('onboarding.vark.submit') }}" id="varkForm">
        @csrf
        @foreach($questions as $index => $q)
        <div class="question-slide {{ $loop->first ? 'active' : '' }}" data-index="{{ $loop->index }}">
            <div class="q-card">
                <div class="q-num">{{ __('messages.onboarding.vark.question', ['current' => $loop->iteration, 'total' => count($questions)]) }}</div>
                <div class="q-text">{{ $q['text'] }}</div>
                <div class="options-grid">
                    @foreach($q['options'] as $key => $label)
                    <label class="option-label">
                        <input type="radio" name="answers[{{ $q['id'] }}]" value="{{ $key }}" required>
                        <span class="option-key">{{ strtoupper($key) }}</span>
                        <span class="option-text">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="q-nav">
                    @if(!$loop->first)
                        <button type="button" class="btn-prev" onclick="navigate({{ $loop->index - 1 }})">{{ __('messages.onboarding.vark.previous') }}</button>
                    @else
                        <span></span>
                    @endif

                    @if(!$loop->last)
                        <button type="button" class="btn-next" id="next-{{ $loop->index }}" onclick="navigate({{ $loop->index + 1 }})" disabled>{{ __('messages.onboarding.vark.next') }}</button>
                    @else
                        <button type="submit" class="btn-submit" id="submitBtn" disabled>
                            🧠 {{ __('messages.onboarding.vark.submit_button') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </form>
</div>
@endsection

@push('scripts')
<script>
let current = 0;
const total = {{ count($questions) }};

function navigate(index) {
    const slides = document.querySelectorAll('.question-slide');
    slides[current].classList.remove('active');
    slides[index].classList.add('active');
    current = index;
    updateProgress();
}

function updateProgress() {
    const pct = ((current + 1) / total) * 100;
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressText').textContent = (current + 1) + ' / ' + total;
}

// Enable Next/Submit when an option is selected
document.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const slide = radio.closest('.question-slide');
        const idx   = parseInt(slide.dataset.index);
        const nextBtn   = document.getElementById('next-' + idx);
        const submitBtn = document.getElementById('submitBtn');
        if (nextBtn) nextBtn.disabled = false;
        if (submitBtn && idx === total - 1) submitBtn.disabled = false;
    });
});
</script>
@endpush