@extends('layouts.app')
@section('title', __('messages.hero.badge'))

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   EDUGENIE WELCOME PAGE
═══════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --primary:   #7C3AED;
    --secondary: #F59E0B;
    --accent:    #10B981;
    --dark:      #0D0D1A;
    --darker:    #07070F;
    /* --darker: linear-gradient(135deg, #FFD6A6, #FFF0BE);; */
    --card-bg:   rgba(255,255,255,0.04);
    --text:      #F8FAFC;
    --muted:     rgba(248,250,252,0.55);
    --border:    rgba(255,255,255,0.08);
    --glow:      rgba(124,58,237,0.35);
}

body.eduvara-body {
    background: var(--darker);
    color: var(--text);
    font-family: 'Inter', sans-serif;
    overflow-x: hidden;
}

/* ─── NAV ─────────────────────────────────── */
.nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 100;
    padding: 1.2rem 6%;
    display: flex; align-items: center; justify-content: space-between;
    backdrop-filter: blur(20px);
    background: rgba(7,7,15,0.75);
    border-bottom: 1px solid var(--border);
}
.nav-logo {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.6rem; font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}
.nav-links { display: flex; gap: 2rem; align-items: center; }
.nav-links a {
    color: var(--muted); text-decoration: none;
    font-size: 0.9rem; font-weight: 500;
    transition: color 0.3s;
}
.nav-links a:hover { color: var(--text); }
.btn-nav {
    background: linear-gradient(135deg, var(--primary), #9333EA);
    color: #fff !important; padding: 0.55rem 1.5rem;
    border-radius: 50px; font-weight: 600; font-size: 0.9rem;
    box-shadow: 0 0 20px var(--glow);
    transition: transform 0.2s, box-shadow 0.2s !important;
}
.btn-nav:hover { transform: translateY(-2px); box-shadow: 0 0 35px var(--glow); }

/* ─── HERO ────────────────────────────────── */
.hero {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden;
    padding: 8rem 6% 4rem;
}
.hero-bg {
    position: absolute; inset: 0; z-index: 0;
    background:
        radial-gradient(ellipse 80% 60% at 50% -20%, rgba(124,58,237,0.3) 0%, transparent 60%),
        radial-gradient(ellipse 50% 40% at 80% 50%, rgba(245,158,11,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 40% 40% at 20% 80%, rgba(16,185,129,0.1) 0%, transparent 60%);
}
.particles-canvas {
    position: absolute; inset: 0; z-index: 1;
}
.hero-inner {
    position: relative; z-index: 2;
    text-align: center; max-width: 900px; margin: 0 auto;
}
.hero-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(124,58,237,0.15);
    border: 1px solid rgba(124,58,237,0.4);
    padding: 0.45rem 1.2rem; border-radius: 50px;
    font-size: 0.85rem; color: #C4B5FD; font-weight: 500;
    margin-bottom: 2rem;
    animation: fadeDown 0.8s ease both;
}
.hero-badge span { width: 6px; height: 6px; border-radius: 50%; background: #7C3AED; animation: pulse 2s infinite; }
.hero-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(2.8rem, 7vw, 5.5rem);
    font-weight: 800; line-height: 1.1;
    letter-spacing: -2px;
    animation: fadeUp 0.9s 0.1s ease both;
}
.hero-title .grad {
    background: linear-gradient(135deg, #A78BFA, #F59E0B, #10B981);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-size: 200%;
    animation: gradShift 4s infinite alternate;
}
.hero-sub {
    font-size: clamp(1rem, 2.5vw, 1.25rem);
    color: var(--muted); max-width: 620px;
    margin: 1.5rem auto 2.5rem;
    line-height: 1.7;
    animation: fadeUp 0.9s 0.2s ease both;
}
.hero-cta {
    display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;
    animation: fadeUp 0.9s 0.3s ease both;
}
.btn-primary {
    background: linear-gradient(135deg, var(--primary), #9333EA);
    color: #fff; padding: 0.9rem 2.5rem;
    border: none; border-radius: 50px; font-size: 1rem; font-weight: 700;
    cursor: pointer; text-decoration: none;
    box-shadow: 0 0 30px var(--glow);
    transition: transform 0.25s, box-shadow 0.25s;
    display: inline-flex; align-items: center; gap: 0.6rem;
}
.btn-primary:hover { transform: translateY(-3px); box-shadow: 0 0 50px var(--glow); }
.btn-outline {
    background: transparent; color: var(--text);
    padding: 0.9rem 2.5rem; border: 1px solid var(--border);
    border-radius: 50px; font-size: 1rem; font-weight: 600;
    cursor: pointer; text-decoration: none;
    backdrop-filter: blur(10px);
    transition: border-color 0.25s, background 0.25s;
    display: inline-flex; align-items: center; gap: 0.6rem;
}
.btn-outline:hover { border-color: var(--primary); background: rgba(124,58,237,0.1); }

/* ─── FLOATING CARDS ──────────────────────── */
.hero-visual {
    position: relative; margin-top: 4rem;
    animation: fadeUp 1s 0.5s ease both;
}
.mock-screen {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 2rem;
    backdrop-filter: blur(20px);
    max-width: 700px; margin: 0 auto;
    box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 60px var(--glow);
}
.mock-bar {
    display: flex; gap: 0.5rem; margin-bottom: 1.5rem;
}
.mock-dot { width: 12px; height: 12px; border-radius: 50%; }
.mock-dot:nth-child(1) { background: #FF5F57; }
.mock-dot:nth-child(2) { background: #FEBC2E; }
.mock-dot:nth-child(3) { background: #28C840; }
.mock-content { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.mock-card {
    background: rgba(255,255,255,0.05);
    border-radius: 12px; padding: 1rem;
    border: 1px solid var(--border);
}
.mock-label { font-size: 0.7rem; color: var(--muted); margin-bottom: 0.5rem; }
.mock-value { font-weight: 700; font-size: 1.1rem; }
.mock-bar-viz { height: 6px; border-radius: 3px; margin-top: 0.4rem; }

/* ─── FEATURES ────────────────────────────── */
.section { padding: 7rem 6%; }
.section-label {
    text-align: center; font-size: 0.8rem; font-weight: 700;
    letter-spacing: 3px; text-transform: uppercase;
    color: var(--secondary); margin-bottom: 0.8rem;
}
.section-title {
    text-align: center;
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 700; margin-bottom: 1rem;
}
.section-sub {
    text-align: center; color: var(--muted);
    max-width: 550px; margin: 0 auto 4rem;
    line-height: 1.7;
}
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.feature-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 20px; padding: 2rem;
    backdrop-filter: blur(10px);
    transition: transform 0.3s, border-color 0.3s, box-shadow 0.3s;
    cursor: default;
}
.feature-card:hover {
    transform: translateY(-8px);
    border-color: rgba(124,58,237,0.4);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 30px var(--glow);
}
.feature-icon {
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: 1.2rem;
}
.feature-icon.purple  { background: rgba(124,58,237,0.2); }
.feature-icon.amber   { background: rgba(245,158,11,0.2); }
.feature-icon.green   { background: rgba(16,185,129,0.2); }
.feature-icon.pink    { background: rgba(236,72,153,0.2); }
.feature-icon.cyan    { background: rgba(6,182,212,0.2);  }
.feature-icon.orange  { background: rgba(249,115,22,0.2); }
.feature-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 0.6rem; }
.feature-desc { color: var(--muted); font-size: 0.9rem; line-height: 1.6; }

/* ─── HOW IT WORKS ────────────────────────── */
.steps-section { background: rgba(124,58,237,0.04); }
.steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem; position: relative;
}
.step-card { text-align: center; position: relative; }
.step-num {
    width: 60px; height: 60px;
    border-radius: 50%; margin: 0 auto 1rem;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.3rem; font-weight: 700;
    background: linear-gradient(135deg, var(--primary), #9333EA);
    box-shadow: 0 0 20px var(--glow);
}
.step-title { font-weight: 700; margin-bottom: 0.5rem; }
.step-desc { color: var(--muted); font-size: 0.88rem; line-height: 1.6; }

/* ─── VARK SECTION ────────────────────────── */
.vark-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}
.vark-card {
    border-radius: 20px; padding: 2rem; text-align: center;
    position: relative; overflow: hidden;
    transition: transform 0.3s;
}
.vark-card:hover { transform: scale(1.04); }
.vark-card.visual       { background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(99,102,241,0.1)); border: 1px solid rgba(124,58,237,0.3); }
.vark-card.auditory     { background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(234,88,12,0.1));  border: 1px solid rgba(245,158,11,0.3); }
.vark-card.reading      { background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(5,150,105,0.1));  border: 1px solid rgba(16,185,129,0.3); }
.vark-card.kinesthetic  { background: linear-gradient(135deg, rgba(236,72,153,0.2), rgba(168,85,247,0.1)); border: 1px solid rgba(236,72,153,0.3); }
.vark-emoji { font-size: 3rem; margin-bottom: 1rem; }
.vark-name { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem; }
.vark-desc { font-size: 0.85rem; color: var(--muted); }

/* ─── CTA SECTION ────────────────────────── */
.cta-section {
    text-align: center; padding: 8rem 6%;
    background: linear-gradient(135deg, rgba(124,58,237,0.1), rgba(245,158,11,0.05));
    position: relative; overflow: hidden;
}
.cta-glow {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 600px; height: 300px;
    background: radial-gradient(ellipse, rgba(124,58,237,0.3) 0%, transparent 70%);
    pointer-events: none;
}

/* ─── FOOTER ──────────────────────────────── */
.footer {
    padding: 2rem 6%; text-align: center;
    border-top: 1px solid var(--border);
    color: var(--muted); font-size: 0.85rem;
}

/* ─── ANIMATIONS ──────────────────────────── */
@keyframes fadeUp   { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: none; } }
@keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: none; } }
@keyframes pulse    { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
@keyframes gradShift { from { background-position: 0% 50%; } to { background-position: 100% 50%; } }
@keyframes float    { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }

.float-anim { animation: float 4s ease-in-out infinite; }

/* ─── SCROLL REVEAL ───────────────────────── */
.reveal { opacity: 0; transform: translateY(40px); transition: opacity 0.7s ease, transform 0.7s ease; }
.reveal.visible { opacity: 1; transform: none; }

/* ─── RESPONSIVE ──────────────────────────── */
@media (max-width: 768px) {
    .nav-links { display: none; }
    .mock-content { grid-template-columns: 1fr; }
    .hero-title { letter-spacing: -1px; }
}

/* Add RTL support */
[dir="rtl"] {
    text-align: right;
}

[dir="rtl"] .nav-links {
    gap: 2rem;
}

[dir="rtl"] .hero-cta {
    flex-direction: row-reverse;
}

[dir="rtl"] .step-card {
    text-align: right;
}

[dir="rtl"] .feature-card {
    text-align: right;
}

/* Language switcher positioning */
.language-switcher {
    margin-left: 1rem;
}

[dir="rtl"] .language-switcher {
    margin-left: 0;
    margin-right: 1rem;
}

[dir="rtl"] .lang-dropdown-content {
    right: auto;
    left: 0;
}
</style>
@endpush

@section('content')

<!-- NAV -->
<nav class="nav">
    <div class="nav-logo">⚡ EDUGENIE</div>
    <div class="nav-links">
        <a href="#features">{{ __('messages.nav.features') }}</a>
        <a href="#how">{{ __('messages.nav.how_it_works') }}</a>
        @include('components.language-switcher')
        @auth
            <a href="{{ route('student.dashboard') }}" class="btn-nav">{{ __('messages.nav.dashboard') }}</a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-nav" style="background:transparent;border:1px solid rgba(255,255,255,0.2);">{{ __('messages.nav.logout') }}</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        @else
            <a href="{{ route('login') }}">{{ __('messages.nav.login') }}</a>
            <a href="{{ route('register') }}" class="btn-nav">{{ __('messages.nav.get_started') }}</a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <canvas class="particles-canvas" id="particles"></canvas>
    <div class="hero-inner">
        <div class="hero-badge">
            <span></span> {{ __('messages.hero.badge') }}
        </div>
        <h1 class="hero-title">
            {{ __('messages.hero.title') }}<br>
            <span class="grad">{{ __('messages.hero.title_gradient') }}</span>
        </h1>
        <p class="hero-sub">
            {{ __('messages.hero.subtitle') }}
        </p>
        <div class="hero-cta">
            @auth
                <a href="{{ route('student.dashboard') }}" class="btn-primary">
                    <i class="fas fa-rocket"></i> {{ __('messages.nav.dashboard') }}
                </a>
            @else
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fas fa-rocket"></i> {{ __('messages.hero.start_learning') }}
                </a>
                <a href="{{ route('login') }}" class="btn-outline">
                    <i class="fas fa-sign-in-alt"></i> {{ __('messages.hero.sign_in') }}
                </a>
            @endauth
        </div>

        <!-- Mock Dashboard Preview -->
        <div class="hero-visual float-anim">
            <div class="mock-screen">
                <div class="mock-bar">
                    <div class="mock-dot"></div>
                    <div class="mock-dot"></div>
                    <div class="mock-dot"></div>
                </div>
                <div class="mock-content">
                    <div class="mock-card">
                        <div class="mock-label">{{ __('messages.student.dashboard.learning_style') }}</div>
                        <div class="mock-value" style="color:#A78BFA">👁️ {{ __('messages.vark_styles.visual.name') }}</div>
                        <div class="mock-bar-viz" style="background:linear-gradient(90deg,#7C3AED,#9333EA);width:80%"></div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-label">{{ __('messages.student.dashboard.videos_generated') }}</div>
                        <div class="mock-value" style="color:#F59E0B">{{ __('messages.student.dashboard.videos_generated_count', ['count' => 24]) }}</div>
                        <div class="mock-bar-viz" style="background:linear-gradient(90deg,#F59E0B,#EF4444);width:65%"></div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-label">{{ __('messages.student.dashboard.quiz_score') }}</div>
                        <div class="mock-value" style="color:#10B981">92%</div>
                        <div class="mock-bar-viz" style="background:linear-gradient(90deg,#10B981,#059669);width:92%"></div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-label">{{ __('messages.student.dashboard.ai_status') }}</div>
                        <div class="mock-value" style="color:#34D399;font-size:0.85rem">● {{ __('messages.student.videos.generating') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="features">
    <p class="section-label">{{ __('messages.features.label') }}</p>
    <h2 class="section-title reveal">{{ __('messages.features.title') }}</h2>
    <p class="section-sub reveal">{{ __('messages.features.subtitle') }}</p>

    <div class="features-grid">
        <div class="feature-card reveal">
            <div class="feature-icon purple">🧠</div>
            <div class="feature-title">{{ __('messages.features.vark.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.vark.desc') }}</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon amber">🎬</div>
            <div class="feature-title">{{ __('messages.features.video.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.video.desc') }}</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon green">📝</div>
            <div class="feature-title">{{ __('messages.features.quiz.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.quiz.desc') }}</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon pink">🔄</div>
            <div class="feature-title">{{ __('messages.features.adaptive.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.adaptive.desc') }}</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon cyan">🌍</div>
            <div class="feature-title">{{ __('messages.features.multilingual.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.multilingual.desc') }}</div>
        </div>
        <div class="feature-card reveal">
            <div class="feature-icon orange">📊</div>
            <div class="feature-title">{{ __('messages.features.analytics.title') }}</div>
            <div class="feature-desc">{{ __('messages.features.analytics.desc') }}</div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section steps-section" id="how">
    <p class="section-label">{{ __('messages.steps.label') }}</p>
    <h2 class="section-title reveal">{{ __('messages.steps.title') }}</h2>
    <p class="section-sub reveal">{{ __('messages.steps.subtitle') }}</p>

    <div class="steps-grid">
        <div class="step-card reveal">
            <div class="step-num">1</div>
            <div class="step-title">{{ __('messages.steps.step1.title') }}</div>
            <div class="step-desc">{{ __('messages.steps.step1.desc') }}</div>
        </div>
        <div class="step-card reveal">
            <div class="step-num">2</div>
            <div class="step-title">{{ __('messages.steps.step2.title') }}</div>
            <div class="step-desc">{{ __('messages.steps.step2.desc') }}</div>
        </div>
        <div class="step-card reveal">
            <div class="step-num">3</div>
            <div class="step-title">{{ __('messages.steps.step3.title') }}</div>
            <div class="step-desc">{{ __('messages.steps.step3.desc') }}</div>
        </div>
        <div class="step-card reveal">
            <div class="step-num">4</div>
            <div class="step-title">{{ __('messages.steps.step4.title') }}</div>
            <div class="step-desc">{{ __('messages.steps.step4.desc') }}</div>
        </div>
        <div class="step-card reveal">
            <div class="step-num">5</div>
            <div class="step-title">{{ __('messages.steps.step5.title') }}</div>
            <div class="step-desc">{{ __('messages.steps.step5.desc') }}</div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="cta-glow"></div>
    <h2 class="section-title reveal" style="position:relative;">{!! __('messages.cta.title') !!}</h2>
    <p class="section-sub reveal" style="position:relative;">{{ __('messages.cta.subtitle') }}</p>
    <div class="hero-cta" style="position:relative;">
        @auth
            <a href="{{ route('student.dashboard') }}" class="btn-primary">
                <i class="fas fa-rocket"></i> {{ __('messages.nav.dashboard') }}
            </a>
        @else
            <a href="{{ route('register') }}" class="btn-primary">
                <i class="fas fa-user-plus"></i> {{ __('messages.cta.create_account') }}
            </a>
        @endauth
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <p>© {{ date('Y') }} {{ __('messages.footer.copyright') }}</p>
</footer>

@endsection

@push('scripts')
<script>
// ─── PARTICLE SYSTEM ────────────────────────────────
const canvas  = document.getElementById('particles');
const ctx     = canvas.getContext('2d');
let W, H, particles = [];

function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
}
window.addEventListener('resize', resize);
resize();

class Particle {
    constructor() { this.reset(); }
    reset() {
        this.x    = Math.random() * W;
        this.y    = Math.random() * H;
        this.r    = Math.random() * 2 + 0.5;
        this.vx   = (Math.random() - 0.5) * 0.4;
        this.vy   = (Math.random() - 0.5) * 0.4;
        this.a    = Math.random() * 0.5 + 0.1;
        this.color= ['#7C3AED','#F59E0B','#10B981','#EC4899','#06B6D4'][Math.floor(Math.random()*5)];
    }
    update() {
        this.x += this.vx; this.y += this.vy;
        if (this.x < 0 || this.x > W || this.y < 0 || this.y > H) this.reset();
    }
    draw() {
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.globalAlpha = this.a;
        ctx.fill();
        ctx.globalAlpha = 1;
    }
}

for (let i = 0; i < 120; i++) particles.push(new Particle());

function animateParticles() {
    ctx.clearRect(0, 0, W, H);
    particles.forEach(p => { p.update(); p.draw(); });

    // Draw connecting lines
    for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
            const dx   = particles[i].x - particles[j].x;
            const dy   = particles[i].y - particles[j].y;
            const dist = Math.sqrt(dx*dx + dy*dy);
            if (dist < 100) {
                ctx.beginPath();
                ctx.moveTo(particles[i].x, particles[i].y);
                ctx.lineTo(particles[j].x, particles[j].y);
                ctx.strokeStyle = 'rgba(124,58,237,' + (0.12 * (1 - dist/100)) + ')';
                ctx.lineWidth = 0.5;
                ctx.stroke();
            }
        }
    }
    requestAnimationFrame(animateParticles);
}
animateParticles();

// ─── SCROLL REVEAL ──────────────────────────────────
const revealEls = document.querySelectorAll('.reveal');
const observer  = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
        if (e.isIntersecting) {
            setTimeout(() => e.target.classList.add('visible'), i * 80);
        }
    });
}, { threshold: 0.1 });
revealEls.forEach(el => observer.observe(el));

// ─── SMOOTH SCROLL ──────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ─── RTL / LTR SUPPORT ──────────────────────────────
const currentLocale = '{{ LaravelLocalization::getCurrentLocale() }}';
if (currentLocale === 'ar') {
    document.documentElement.setAttribute('dir', 'rtl');
    document.body.classList.add('rtl');
} else {
    document.documentElement.setAttribute('dir', 'ltr');
    document.body.classList.remove('rtl');
}
</script>
@endpush
