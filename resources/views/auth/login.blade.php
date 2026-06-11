<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.login.title') }} — EDUGENIE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --primary:  #7C3AED;
        --secondary:#F59E0B;
        --accent:   #10B981;
        --darker:   #07070F;
        --dark:     #0D0D1A;
        --card:     rgba(255,255,255,0.04);
        --text:     #F8FAFC;
        --muted:    rgba(248,250,252,0.5);
        --border:   rgba(255,255,255,0.08);
        --glow:     rgba(124,58,237,0.4);
        --input-bg: rgba(255,255,255,0.05);
        --error:    #FCA5A5;
    }

    html, body {
        min-height: 100vh;
        background: var(--darker);
        color: var(--text);
        font-family: 'Inter', sans-serif;
    }

    /* RTL Support */
    [dir="rtl"] .field-icon {
        left: auto;
        right: 1rem;
    }
    
    [dir="rtl"] .field-input {
        padding: 0.85rem 2.8rem 0.85rem 1rem;
    }
    
    [dir="rtl"] .pass-toggle {
        right: auto;
        left: 1rem;
    }

    /* ── Layout ─────────────────────────────── */
    .auth-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 100vh;
    }
    @media(max-width:860px) {
        .auth-wrap { grid-template-columns: 1fr; }
        .auth-panel { display: none; }
    }

    /* ── Left decorative panel ───────────────── */
    .auth-panel {
        position: relative; overflow: hidden;
        background: linear-gradient(160deg, #0D0D1A 0%, #1A0A3A 50%, #0A1A2A 100%);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 3rem;
    }
    .auth-panel::before {
        content: '';
        position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 70% 50% at 30% 30%, rgba(124,58,237,0.25) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 80% 70%, rgba(245,158,11,0.12) 0%, transparent 60%),
            radial-gradient(ellipse 40% 30% at 60% 10%, rgba(16,185,129,0.1)  0%, transparent 50%);
    }
    .panel-canvas { position: absolute; inset: 0; }

    .panel-content {
        position: relative; z-index: 2;
        text-align: center;
    }
    .panel-logo {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 2rem; font-weight: 700;
        background: linear-gradient(135deg, #A78BFA, #F59E0B);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 3rem;
    }
    .panel-title {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 2.2rem; font-weight: 700;
        line-height: 1.25; margin-bottom: 1.2rem;
    }
    .panel-sub {
        color: var(--muted); line-height: 1.7;
        font-size: 0.95rem; max-width: 340px;
        margin: 0 auto 2.5rem;
    }

    /* VARK mini cards */
    .vark-mini {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 0.8rem; max-width: 340px; margin: 0 auto;
    }
    .vark-mini-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 14px; padding: 1rem;
        text-align: center;
        transition: transform 0.3s;
    }
    .vark-mini-card:hover { transform: translateY(-3px); }
    .vark-mini-card .emoji { font-size: 1.5rem; margin-bottom: 0.4rem; }
    .vark-mini-card .label { font-size: 0.75rem; font-weight: 600; color: var(--muted); }

    /* Floating orbs */
    .orb {
        position: absolute; border-radius: 50%;
        filter: blur(60px); pointer-events: none;
        animation: orbFloat 6s ease-in-out infinite;
    }
    .orb-1 { width:300px;height:300px;background:rgba(124,58,237,0.2);top:-80px;left:-80px; }
    .orb-2 { width:200px;height:200px;background:rgba(245,158,11,0.15);bottom:80px;right:-60px;animation-delay:2s; }
    .orb-3 { width:150px;height:150px;background:rgba(16,185,129,0.12);top:50%;left:50%;transform:translate(-50%,-50%);animation-delay:4s; }
    @keyframes orbFloat {
        0%,100% { transform: translateY(0) scale(1); }
        50%      { transform: translateY(-20px) scale(1.05); }
    }

    /* ── Right form side ─────────────────────── */
    .auth-form-side {
        display: flex; align-items: center; justify-content: center;
        padding: 2rem; background: var(--darker);
        position: relative;
    }
    .auth-form-side::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(124,58,237,0.06) 0%, transparent 60%);
        pointer-events: none;
    }

    .form-box {
        width: 100%; max-width: 420px;
        position: relative; z-index: 1;
    }

    /* Mobile logo (shown only on small screens) */
    .mobile-logo {
        display: none; text-align: center; margin-bottom: 2rem;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.6rem; font-weight: 700;
        background: linear-gradient(135deg, #A78BFA, #F59E0B);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    @media(max-width:860px) { .mobile-logo { display: block; } }

    .form-heading { font-family: 'Space Grotesk', sans-serif; font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; }
    .form-sub     { color: var(--muted); font-size: 0.9rem; margin-bottom: 2.2rem; }

    /* Error alert */
    .alert-error {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 12px; padding: 0.9rem 1.1rem;
        color: var(--error); font-size: 0.85rem;
        display: flex; align-items: flex-start; gap: 0.6rem;
        margin-bottom: 1.5rem;
    }

    /* Form groups */
    .field { margin-bottom: 1.3rem; position: relative; }
    .field-label {
        display: block; font-size: 0.78rem; font-weight: 600;
        color: var(--muted); text-transform: uppercase;
        letter-spacing: 1px; margin-bottom: 0.55rem;
    }
    .field-wrap { position: relative; }
    .field-icon {
        position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); font-size: 0.9rem; pointer-events: none;
        transition: color 0.2s;
    }
    .field-input {
        width: 100%;
        background: var(--input-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 0.85rem 1rem 0.85rem 2.8rem;
        color: var(--text); font-size: 0.92rem;
        font-family: 'Inter', sans-serif;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .field-input:focus {
        border-color: var(--primary);
        background: rgba(124,58,237,0.06);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
    }
    .field-input:focus ~ .field-icon,
    .field-wrap:focus-within .field-icon { color: #A78BFA; }
    .field-input.is-error { border-color: rgba(239,68,68,0.5); }
    .field-error { font-size: 0.75rem; color: var(--error); margin-top: 0.4rem; }

    /* Password toggle */
    .pass-toggle {
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); cursor: pointer; font-size: 0.9rem;
        background: none; border: none; padding: 0.2rem;
        transition: color 0.2s;
    }
    .pass-toggle:hover { color: var(--text); }

    /* Remember + Forgot row */
    .form-row {
        display: flex; align-items: center;
        justify-content: space-between; margin-bottom: 1.5rem;
    }
    .checkbox-wrap {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.85rem; color: var(--muted); cursor: pointer;
    }
    .checkbox-wrap input[type="checkbox"] {
        width: 16px; height: 16px; border-radius: 4px;
        accent-color: var(--primary); cursor: pointer;
    }
    .forgot-link {
        font-size: 0.83rem; color: #A78BFA;
        text-decoration: none; transition: color 0.2s;
    }
    .forgot-link:hover { color: #C4B5FD; text-decoration: underline; }

    /* Submit button */
    .btn-submit {
        width: 100%; padding: 0.95rem;
        background: linear-gradient(135deg, #7C3AED, #9333EA);
        color: #fff; border: none; border-radius: 12px;
        font-size: 1rem; font-weight: 700;
        font-family: 'Inter', sans-serif;
        cursor: pointer; position: relative; overflow: hidden;
        box-shadow: 0 0 25px var(--glow);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-submit::before {
        content: '';
        position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
        opacity: 0; transition: opacity 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 40px var(--glow);
    }
    .btn-submit:hover::before { opacity: 1; }
    .btn-submit:active { transform: translateY(0); }

    /* Divider */
    .divider {
        display: flex; align-items: center; gap: 1rem;
        margin: 1.5rem 0; color: var(--muted); font-size: 0.78rem;
    }
    .divider::before, .divider::after {
        content: ''; flex: 1; height: 1px; background: var(--border);
    }

    /* Register link */
    .auth-switch {
        text-align: center; font-size: 0.88rem; color: var(--muted);
    }
    .auth-switch a {
        color: #A78BFA; text-decoration: none; font-weight: 600;
        transition: color 0.2s;
    }
    .auth-switch a:hover { color: #C4B5FD; }

    /* Loading state */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner {
        display: inline-block; width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff; border-radius: 50%;
        animation: spin 0.7s linear infinite;
        vertical-align: middle; margin-right: 0.4rem;
    }
    </style>
</head>
<body>

<div class="auth-wrap">

    <!-- ── Left Panel ───────────────────────── -->
    <div class="auth-panel">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <canvas class="panel-canvas" id="panelCanvas"></canvas>

        <div class="panel-content">
            <div class="panel-logo">⚡ EDUGENIE</div>
            <h2 class="panel-title">{!! __('messages.login.panel_title') !!}</h2>
            <p class="panel-sub">{{ __('messages.login.panel_subtitle') }}</p>

            <div class="vark-mini">
                <div class="vark-mini-card">
                    <div class="emoji">👁️</div>
                    <div class="label">{{ __('messages.vark_styles.visual.name') }}</div>
                </div>
                <div class="vark-mini-card">
                    <div class="emoji">👂</div>
                    <div class="label">{{ __('messages.vark_styles.auditory.name') }}</div>
                </div>
                <div class="vark-mini-card">
                    <div class="emoji">📖</div>
                    <div class="label">{{ __('messages.vark_styles.reading.name') }}</div>
                </div>
                <div class="vark-mini-card">
                    <div class="emoji">🤲</div>
                    <div class="label">{{ __('messages.vark_styles.kinesthetic.name') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Right Form Side ───────────────────── -->
    <div class="auth-form-side">
        <div class="form-box">

            <div class="mobile-logo">⚡ EDUGENIE</div>

            <h1 class="form-heading">{{ __('messages.login.welcome_back') }}</h1>
            <p class="form-sub">{{ __('messages.login.sign_in_continue') }}</p>

            {{-- Error Alert --}}
            @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf

                {{-- Email --}}
                <div class="field">
                    <label class="field-label" for="email">{{ __('messages.login.email_address') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-envelope field-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="{{ __('messages.login.email_placeholder') }}"
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field">
                    <label class="field-label" for="password">{{ __('messages.login.password') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="{{ __('messages.login.password_placeholder') }}"
                            autocomplete="current-password"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-row">
                    <label class="checkbox-wrap">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        {{ __('messages.login.remember_me') }}
                    </label>
                    <a href="#" class="forgot-link">{{ __('messages.login.forgot_password') }}</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit" id="submitBtn">
                    {{ __('messages.login.sign_in') }}
                </button>
            </form>

            <div class="divider">{{ __('messages.login.or') }}</div>

            <div class="auth-switch">
                {{ __('messages.login.no_account') }} <a href="{{ route('register') }}">{{ __('messages.login.create_one') }} →</a>
            </div>

        </div>
    </div>
</div>

<script>
// Password visibility toggle
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Loading state on submit
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner"></span> {{ __('messages.login.signing_in') }}...';
    btn.disabled  = true;
});

// ── Particle canvas (left panel) ─────────────
const canvas = document.getElementById('panelCanvas');
const ctx    = canvas.getContext('2d');
let W, H, pts = [];

function resize() {
    W = canvas.width  = canvas.offsetWidth;
    H = canvas.height = canvas.offsetHeight;
}
window.addEventListener('resize', resize);
resize();

class Dot {
    constructor() { this.reset(); }
    reset() {
        this.x  = Math.random() * W;
        this.y  = Math.random() * H;
        this.r  = Math.random() * 1.8 + 0.4;
        this.vx = (Math.random() - 0.5) * 0.35;
        this.vy = (Math.random() - 0.5) * 0.35;
        this.a  = Math.random() * 0.45 + 0.1;
        this.c  = ['#7C3AED','#A78BFA','#F59E0B','#10B981'][Math.floor(Math.random()*4)];
    }
    update() {
        this.x += this.vx; this.y += this.vy;
        if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
    }
    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = this.c;
        ctx.globalAlpha = this.a;
        ctx.fill();
        ctx.globalAlpha = 1;
    }
}

for (let i = 0; i < 80; i++) pts.push(new Dot());

(function animate() {
    ctx.clearRect(0, 0, W, H);
    pts.forEach(p => { p.update(); p.draw(); });
    for (let i = 0; i < pts.length; i++) {
        for (let j = i+1; j < pts.length; j++) {
            const dx = pts[i].x - pts[j].x;
            const dy = pts[i].y - pts[j].y;
            const d  = Math.sqrt(dx*dx + dy*dy);
            if (d < 90) {
                ctx.beginPath();
                ctx.moveTo(pts[i].x, pts[i].y);
                ctx.lineTo(pts[j].x, pts[j].y);
                ctx.strokeStyle = `rgba(124,58,237,${0.1*(1-d/90)})`;
                ctx.lineWidth   = 0.5;
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(animate);
})();
</script>

</body>
</html>