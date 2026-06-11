<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.register.title') }} — EDUGENIE</title>
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
        --text:     #F8FAFC;
        --muted:    rgba(248,250,252,0.5);
        --border:   rgba(255,255,255,0.08);
        --glow:     rgba(124,58,237,0.4);
        --input-bg: rgba(255,255,255,0.05);
        --error:    #FCA5A5;
        --success:  #34D399;
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
        left: 2.2rem;
    }

    [dir="rtl"] .valid-icon {
        right: auto;
        left: 1rem;
    }

    [dir="rtl"] .step-item {
        text-align: right;
    }

    [dir="rtl"] .step-item:hover {
        transform: translateX(-4px);
    }

    .auth-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        min-height: 100vh;
    }
    @media(max-width:860px) {
        .auth-wrap { grid-template-columns: 1fr; }
        .auth-panel { display: none; }
    }

    /* ── Left Panel ─────────────────────────── */
    .auth-panel {
        position: relative; overflow: hidden;
        background: linear-gradient(160deg, #0D0D1A 0%, #0A2A1A 50%, #1A0A3A 100%);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 3rem;
    }
    .auth-panel::before {
        content: ''; position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 70% 50% at 30% 30%, rgba(16,185,129,0.2)  0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 80% 70%, rgba(124,58,237,0.15) 0%, transparent 60%),
            radial-gradient(ellipse 40% 30% at 60% 10%, rgba(245,158,11,0.1)  0%, transparent 50%);
    }
    .panel-canvas { position: absolute; inset: 0; }
    .panel-content { position: relative; z-index: 2; text-align: center; }

    .panel-logo {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 2rem; font-weight: 700;
        background: linear-gradient(135deg, #34D399, #A78BFA);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        margin-bottom: 3rem;
    }
    .panel-title {
        font-family: 'Space Grotesk', sans-serif;
        font-size: 2.1rem; font-weight: 700;
        line-height: 1.25; margin-bottom: 1.2rem;
    }
    .panel-sub {
        color: var(--muted); line-height: 1.7;
        font-size: 0.92rem; max-width: 340px;
        margin: 0 auto 2.5rem;
    }

    /* Steps visual */
    .steps-visual {
        display: flex; flex-direction: column;
        gap: 1rem; max-width: 320px; margin: 0 auto;
        text-align: left;
    }
    [dir="rtl"] .steps-visual {
        text-align: right;
    }
    .step-item {
        display: flex; align-items: center; gap: 1rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 14px; padding: 0.9rem 1.1rem;
        transition: transform 0.3s;
    }
    .step-item:hover { transform: translateX(4px); }
    [dir="rtl"] .step-item:hover { transform: translateX(-4px); }
    .step-num {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.8rem; flex-shrink: 0;
    }
    .step-num.s1 { background: linear-gradient(135deg,#7C3AED,#9333EA); }
    .step-num.s2 { background: linear-gradient(135deg,#F59E0B,#EF4444); }
    .step-num.s3 { background: linear-gradient(135deg,#10B981,#059669); }
    .step-num.s4 { background: linear-gradient(135deg,#EC4899,#A855F7); }
    .step-text { font-size: 0.82rem; font-weight: 500; }
    .step-text span { color: var(--muted); font-weight: 400; display: block; font-size: 0.75rem; }

    /* Orbs */
    .orb {
        position: absolute; border-radius: 50%;
        filter: blur(70px); pointer-events: none;
        animation: orbFloat 7s ease-in-out infinite;
    }
    .orb-1 { width:280px;height:280px;background:rgba(16,185,129,0.18);top:-60px;right:-60px; }
    .orb-2 { width:200px;height:200px;background:rgba(124,58,237,0.15);bottom:60px;left:-40px;animation-delay:3s; }
    @keyframes orbFloat { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-18px)} }

    /* ── Right Form ──────────────────────────── */
    .auth-form-side {
        display: flex; align-items: center; justify-content: center;
        padding: 2rem; background: var(--darker);
        position: relative; overflow-y: auto;
    }
    .auth-form-side::before {
        content: ''; position: absolute; inset: 0;
        background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(16,185,129,0.05) 0%, transparent 60%);
        pointer-events: none;
    }

    .form-box {
        width: 100%; max-width: 420px;
        position: relative; z-index: 1;
        padding: 1rem 0;
    }

    .mobile-logo {
        display: none; text-align: center; margin-bottom: 2rem;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.6rem; font-weight: 700;
        background: linear-gradient(135deg, #34D399, #A78BFA);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    @media(max-width:860px) { .mobile-logo { display: block; } }

    .form-heading { font-family:'Space Grotesk',sans-serif; font-size:1.8rem; font-weight:700; margin-bottom:0.5rem; }
    .form-sub     { color:var(--muted); font-size:0.9rem; margin-bottom:2rem; }

    /* Alert */
    .alert-error {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 12px; padding: 0.9rem 1.1rem;
        color: var(--error); font-size: 0.85rem;
        display: flex; align-items: flex-start; gap: 0.6rem;
        margin-bottom: 1.5rem;
    }

    /* Fields */
    .field { margin-bottom: 1.15rem; position: relative; }
    .field-label {
        display: block; font-size: 0.78rem; font-weight: 600;
        color: var(--muted); text-transform: uppercase;
        letter-spacing: 1px; margin-bottom: 0.5rem;
    }
    .field-wrap { position: relative; }
    .field-icon {
        position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); font-size: 0.88rem; pointer-events: none;
        transition: color 0.2s;
    }
    .field-wrap:focus-within .field-icon { color: #A78BFA; }
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
    .field-input.is-error  { border-color: rgba(239,68,68,0.5); }
    .field-input.is-valid  { border-color: rgba(16,185,129,0.5); }
    .field-error { font-size: 0.75rem; color: var(--error); margin-top: 0.4rem; }

    /* Validation icon */
    .valid-icon {
        position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
        font-size: 0.85rem; display: none;
    }
    .valid-icon.show-valid   { display: block; color: var(--success); }
    .valid-icon.show-invalid { display: block; color: var(--error); }

    /* Password toggle */
    .pass-toggle {
        position: absolute; right: 2.2rem; top: 50%; transform: translateY(-50%);
        color: var(--muted); cursor: pointer; font-size: 0.88rem;
        background: none; border: none; padding: 0.2rem;
        transition: color 0.2s;
    }
    .pass-toggle:hover { color: var(--text); }

    /* Password strength */
    .strength-wrap { margin-top: 0.5rem; }
    .strength-bars { display: flex; gap: 4px; margin-bottom: 0.3rem; }
    .strength-bar {
        flex: 1; height: 4px; border-radius: 2px;
        background: rgba(255,255,255,0.08);
        transition: background 0.3s;
    }
    .strength-label { font-size: 0.72rem; color: var(--muted); }

    /* Submit */
    .btn-submit {
        width: 100%; padding: 0.95rem;
        background: linear-gradient(135deg, #10B981, #059669);
        color: #fff; border: none; border-radius: 12px;
        font-size: 1rem; font-weight: 700;
        font-family: 'Inter', sans-serif;
        cursor: pointer; position: relative; overflow: hidden;
        box-shadow: 0 0 25px rgba(16,185,129,0.4);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 40px rgba(16,185,129,0.5);
    }
    .btn-submit:active { transform: translateY(0); }

    /* Auth switch */
    .divider { display:flex;align-items:center;gap:1rem;margin:1.4rem 0;color:var(--muted);font-size:0.78rem; }
    .divider::before,.divider::after { content:'';flex:1;height:1px;background:var(--border); }
    .auth-switch { text-align:center;font-size:0.88rem;color:var(--muted); }
    .auth-switch a { color:#A78BFA;text-decoration:none;font-weight:600;transition:color 0.2s; }
    .auth-switch a:hover { color:#C4B5FD; }

    /* Spinner */
    @keyframes spin { to{transform:rotate(360deg)} }
    .spinner { display:inline-block;width:16px;height:16px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite;vertical-align:middle;margin-right:0.4rem; }
    </style>
</head>
<body>

<div class="auth-wrap">

    <!-- ── Left Panel ───────────────────────── -->
    <div class="auth-panel">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <canvas class="panel-canvas" id="panelCanvas"></canvas>

        <div class="panel-content">
            <div class="panel-logo">⚡ EDUGENIE</div>
            <h2 class="panel-title">{!! __('messages.register.panel_title') !!}</h2>
            <p class="panel-sub">{{ __('messages.register.panel_subtitle') }}</p>

            <div class="steps-visual">
                <div class="step-item">
                    <div class="step-num s1">1</div>
                    <div class="step-text">{{ __('messages.register.step1_title') }}
                        <span>{{ __('messages.register.step1_desc') }}</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num s2">2</div>
                    <div class="step-text">{{ __('messages.register.step2_title') }}
                        <span>{{ __('messages.register.step2_desc') }}</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num s3">3</div>
                    <div class="step-text">{{ __('messages.register.step3_title') }}
                        <span>{{ __('messages.register.step3_desc') }}</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num s4">4</div>
                    <div class="step-text">{{ __('messages.register.step4_title') }}
                        <span>{{ __('messages.register.step4_desc') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Right Form Side ───────────────────── -->
    <div class="auth-form-side">
        <div class="form-box">

            <div class="mobile-logo">⚡ EDUGENIE</div>

            <h1 class="form-heading">{{ __('messages.register.create_account') }}</h1>
            <p class="form-sub">{{ __('messages.register.start_learning') }}</p>

            @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle" style="margin-top:2px;flex-shrink:0;"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate>
                @csrf

                {{-- Full Name --}}
                <div class="field">
                    <label class="field-label" for="name">{{ __('messages.register.full_name') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-user field-icon"></i>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="field-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="{{ __('messages.register.name_placeholder') }}"
                            autocomplete="name"
                            autofocus
                        >
                        <i class="fas fa-check valid-icon" id="nameIcon"></i>
                    </div>
                    @error('name')
                        <div class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="field">
                    <label class="field-label" for="email">{{ __('messages.register.email_address') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-envelope field-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="{{ __('messages.register.email_placeholder') }}"
                            autocomplete="email"
                        >
                        <i class="fas fa-check valid-icon" id="emailIcon"></i>
                    </div>
                    @error('email')
                        <div class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field">
                    <label class="field-label" for="password">{{ __('messages.register.password') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="field-input {{ $errors->has('password') ? 'is-error' : '' }}"
                            placeholder="{{ __('messages.register.password_placeholder') }}"
                            autocomplete="new-password"
                            oninput="checkStrength(this.value)"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                        <i class="fas fa-check valid-icon" id="passIcon"></i>
                    </div>
                    @error('password')
                        <div class="field-error"><i class="fas fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror

                    {{-- Strength meter --}}
                    <div class="strength-wrap">
                        <div class="strength-bars">
                            <div class="strength-bar" id="sb1"></div>
                            <div class="strength-bar" id="sb2"></div>
                            <div class="strength-bar" id="sb3"></div>
                            <div class="strength-bar" id="sb4"></div>
                        </div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="field">
                    <label class="field-label" for="password_confirmation">{{ __('messages.register.confirm_password') }}</label>
                    <div class="field-wrap">
                        <i class="fas fa-shield-halved field-icon"></i>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="field-input"
                            placeholder="{{ __('messages.register.confirm_placeholder') }}"
                            autocomplete="new-password"
                            oninput="checkMatch()"
                        >
                        <button type="button" class="pass-toggle" onclick="togglePass('password_confirmation', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                        <i class="fas fa-check valid-icon" id="confirmIcon"></i>
                    </div>
                    <div class="field-error" id="matchError" style="display:none;">
                        <i class="fas fa-circle-exclamation"></i> {{ __('messages.register.password_mismatch') }}
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-rocket"></i> {{ __('messages.register.create_button') }}
                </button>
            </form>

            <div class="divider">{{ __('messages.register.or') }}</div>

            <div class="auth-switch">
                {{ __('messages.register.have_account') }} <a href="{{ route('login') }}">{{ __('messages.register.sign_in_link') }} →</a>
            </div>

        </div>
    </div>
</div>

<script>
// ── Password visibility ──────────────────────
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

// ── Password strength meter ──────────────────
function checkStrength(val) {
    const bars   = [sb1, sb2, sb3, sb4];
    const label  = document.getElementById('strengthLabel');
    const icon   = document.getElementById('passIcon');
    const colors = ['#EF4444','#F59E0B','#3B82F6','#10B981'];
    const labels = [
        '{{ __("messages.register.weak") }}',
        '{{ __("messages.register.fair") }}',
        '{{ __("messages.register.good") }}',
        '{{ __("messages.register.strong") }}'
    ];

    let score = 0;
    if (val.length >= 8)                score++;
    if (/[A-Z]/.test(val))             score++;
    if (/[0-9]/.test(val))             score++;
    if (/[^A-Za-z0-9]/.test(val))      score++;

    bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.08)';
    });

    if (val.length === 0) {
        label.textContent = '';
        icon.className = 'fas fa-check valid-icon';
    } else {
        label.textContent = labels[score - 1] || '{{ __("messages.register.too_short") }}';
        label.style.color = score >= 3 ? '#34D399' : colors[score - 1] || '#EF4444';
        icon.className = 'fas fa-check valid-icon ' + (score >= 2 ? 'show-valid' : 'show-invalid');
        icon.classList.replace('fa-check', score >= 2 ? 'fa-check' : 'fa-xmark');
    }

    checkMatch();
}

// ── Password match check ─────────────────────
function checkMatch() {
    const pass    = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const icon    = document.getElementById('confirmIcon');
    const err     = document.getElementById('matchError');

    if (confirm.length === 0) {
        icon.className = 'fas fa-check valid-icon';
        err.style.display = 'none';
        return;
    }

    const match = pass === confirm;
    icon.className = `fas ${match ? 'fa-check show-valid' : 'fa-xmark show-invalid'} valid-icon`;
    err.style.display = match ? 'none' : 'block';
    document.getElementById('password_confirmation').className =
        'field-input ' + (match ? 'is-valid' : 'is-error');
}

// ── Name / Email live validation ─────────────
document.getElementById('name').addEventListener('input', function() {
    const icon = document.getElementById('nameIcon');
    const valid = this.value.trim().length >= 2;
    icon.className = `fas ${valid ? 'fa-check show-valid' : 'fa-xmark show-invalid'} valid-icon`;
    this.className = 'field-input ' + (valid ? 'is-valid' : '');
});

document.getElementById('email').addEventListener('input', function() {
    const icon  = document.getElementById('emailIcon');
    const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
    icon.className = `fas ${valid ? 'fa-check show-valid' : 'fa-xmark show-invalid'} valid-icon`;
    this.className = 'field-input ' + (valid ? 'is-valid' : '');
});

// ── Submit loading state ─────────────────────
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const pass    = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    if (pass !== confirm) { e.preventDefault(); return; }
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<span class="spinner"></span> {{ __("messages.register.creating") }}...';
    btn.disabled  = true;
});

// ── Particle canvas ──────────────────────────
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
        this.vx = (Math.random() - 0.5) * 0.3;
        this.vy = (Math.random() - 0.5) * 0.3;
        this.a  = Math.random() * 0.4 + 0.1;
        this.c  = ['#10B981','#34D399','#7C3AED','#F59E0B'][Math.floor(Math.random()*4)];
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
            if (d < 85) {
                ctx.beginPath();
                ctx.moveTo(pts[i].x, pts[i].y);
                ctx.lineTo(pts[j].x, pts[j].y);
                ctx.strokeStyle = `rgba(16,185,129,${0.1*(1-d/85)})`;
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