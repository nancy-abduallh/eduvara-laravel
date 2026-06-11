@extends('layouts.student')
@section('title', __('messages.student.dashboard.title'))
@section('page-title', __('messages.student.dashboard.page_title'))

@push('styles')
<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.2rem; margin-bottom: 2.5rem;
}
.stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 18px; padding: 1.5rem;
    position: relative; overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.3); }
.stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
}
.stat-card.purple::before { background: linear-gradient(90deg, #7C3AED, #9333EA); }
.stat-card.amber::before  { background: linear-gradient(90deg, #F59E0B, #EF4444); }
.stat-card.green::before  { background: linear-gradient(90deg, #10B981, #059669); }
.stat-card.pink::before   { background: linear-gradient(90deg, #EC4899, #A855F7); }
.stat-icon { font-size: 1.8rem; margin-bottom: 0.8rem; }
.stat-value { font-family: 'Space Grotesk', sans-serif; font-size: 2rem; font-weight: 800; }
.stat-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; margin-top: 0.3rem; }

/* Generate section */
.generate-panel {
    background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(245,158,11,0.08));
    border: 1px solid rgba(124,58,237,0.3);
    border-radius: 20px; padding: 2rem;
    margin-bottom: 2.5rem;
}
.generate-panel h2 { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.2rem; }
.gen-form { display: flex; gap: 1rem; flex-wrap: wrap; }
.gen-input {
    flex: 1; min-width: 200px;
    background: rgba(255,255,255,0.06);
    border: 1px solid var(--border);
    border-radius: 12px; padding: 0.85rem 1.2rem;
    color: var(--text); font-size: 0.95rem;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.2s;
}
.gen-input:focus { outline: none; border-color: var(--primary); }
.gen-input::placeholder { color: var(--muted); }
.btn-generate {
    background: linear-gradient(135deg, #7C3AED, #9333EA);
    color: #fff; border: none; border-radius: 12px;
    padding: 0.85rem 2rem; font-weight: 700; font-size: 0.95rem;
    cursor: pointer; white-space: nowrap;
    box-shadow: 0 0 20px var(--glow);
    transition: all 0.25s; display: flex; align-items: center; gap: 0.5rem;
}
.btn-generate:hover { transform: translateY(-2px); box-shadow: 0 0 35px var(--glow); }
.btn-generate.loading { opacity: 0.7; cursor: not-allowed; }

/* Video cards */
.section-head {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 1.2rem;
}
.section-head h2 { font-size: 1.1rem; font-weight: 700; }
.see-all { font-size: 0.85rem; color: var(--primary); text-decoration: none; }
.see-all:hover { text-decoration: underline; }
[dir="rtl"] .section-head {
    flex-direction: row-reverse;
}
.videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.2rem; margin-bottom: 2.5rem;
}
.video-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    text-decoration: none; color: inherit;
    display: block;
}
.video-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
.video-thumb {
    height: 140px;
    background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(245,158,11,0.2));
    display: flex; align-items: center; justify-content: center;
    font-size: 2.5rem; position: relative;
}
.video-thumb img { width: 100%; height: 100%; object-fit: cover; }
.status-badge {
    position: absolute; top: 0.6rem; right: 0.6rem;
    padding: 0.2rem 0.7rem; border-radius: 50px;
    font-size: 0.7rem; font-weight: 700;
}
[dir="rtl"] .status-badge {
    right: auto;
    left: 0.6rem;
}
.status-completed { background: rgba(16,185,129,0.2); color: #34D399; border: 1px solid rgba(16,185,129,0.3); }
.status-processing { background: rgba(245,158,11,0.2); color: #FCD34D; border: 1px solid rgba(245,158,11,0.3); }
.status-queued     { background: rgba(99,102,241,0.2); color: #A5B4FC; border: 1px solid rgba(99,102,241,0.3); }
.status-failed     { background: rgba(239,68,68,0.2); color: #FCA5A5; border: 1px solid rgba(239,68,68,0.3); }
.video-info { padding: 1rem; }
.video-title { font-weight: 600; font-size: 0.9rem; margin-bottom: 0.4rem; line-height: 1.4; }
.video-meta { font-size: 0.75rem; color: var(--muted); }

/* Recent quiz */
.recent-table {
    width: 100%; border-collapse: collapse;
    font-size: 0.88rem;
}
.recent-table th {
    text-align: left; padding: 0.7rem 1rem;
    font-size: 0.75rem; font-weight: 600;
    color: var(--muted); text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid var(--border);
}
[dir="rtl"] .recent-table th {
    text-align: right;
}
.recent-table td {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.score-pill {
    display: inline-block; padding: 0.2rem 0.8rem;
    border-radius: 50px; font-weight: 700; font-size: 0.8rem;
}
.score-pass { background: rgba(16,185,129,0.2); color: #34D399; }
.score-fail { background: rgba(239,68,68,0.2); color: #FCA5A5; }

/* Pending queue */
.pending-banner {
    background: rgba(245,158,11,0.1);
    border: 1px solid rgba(245,158,11,0.25);
    border-radius: 14px; padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    margin-bottom: 1.5rem;
}
.pending-dot { width: 10px; height: 10px; border-radius: 50%; background: #F59E0B; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }
.panel { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 1.5rem; margin-bottom: 1.5rem; }
.empty-state { text-align: center; padding: 3rem 1rem; color: var(--muted); }
.empty-icon { font-size: 3rem; margin-bottom: 1rem; }
</style>
@endpush

@section('content')

<!-- Pending Banner -->
@if($pending > 0)
<div class="pending-banner">
    <div class="pending-dot"></div>
    <span>{!! __('messages.student.dashboard.pending_message', ['count' => $pending]) !!}</span>
    <button onclick="window.location.reload()" style="margin-left:auto;background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.3);color:#FCD34D;padding:0.3rem 0.8rem;border-radius:8px;cursor:pointer;font-size:0.8rem;">{{ __('messages.student.dashboard.refresh') }}</button>
</div>
@endif

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-icon">🎬</div>
        <div class="stat-value">{{ $stats['total_videos'] }}</div>
        <div class="stat-label">{{ __('messages.student.dashboard.total_videos') }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $stats['completed'] }}</div>
        <div class="stat-label">{{ __('messages.student.dashboard.completed') }}</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon">📝</div>
        <div class="stat-value">{{ $stats['quiz_taken'] }}</div>
        <div class="stat-label">{{ __('messages.student.dashboard.quizzes_taken') }}</div>
    </div>
    <div class="stat-card pink">
        <div class="stat-icon">🏆</div>
        <div class="stat-value">{{ round($stats['avg_score']) }}%</div>
        <div class="stat-label">{{ __('messages.student.dashboard.avg_score') }}</div>
    </div>
</div>

<!-- Generate Video Panel -->
<div class="generate-panel">
    <h2>{{ __('messages.student.dashboard.generate_title') }}</h2>
    <div class="gen-form" id="genForm">
        <input type="text" class="gen-input" id="genTopic" placeholder="{{ __('messages.student.dashboard.topic_placeholder') }}" maxlength="500">
        <input type="text" class="gen-input" id="genCaption" placeholder="{{ __('messages.student.dashboard.caption_placeholder') }}" maxlength="255">
        <button class="btn-generate" id="genBtn" onclick="requestVideo()">
            <i class="fas fa-magic"></i> {{ __('messages.student.dashboard.generate_button') }}
        </button>
    </div>
    <div id="genStatus" style="margin-top:1rem;font-size:0.88rem;color:var(--muted);display:none;"></div>
</div>

<!-- Recent Videos -->
<div class="section-head">
    <h2>{{ __('messages.student.dashboard.recent_videos') }}</h2>
    <a href="{{ route('student.videos') }}" class="see-all">{{ __('messages.student.dashboard.see_all') }} →</a>
</div>

@if($videos->isEmpty())
<div class="panel">
    <div class="empty-state">
        <div class="empty-icon">🎬</div>
        <p>{{ __('messages.student.dashboard.no_videos') }}</p>
    </div>
</div>
@else
<div class="videos-grid">
    @foreach($videos as $video)
    <a href="{{ route('student.videos.show', $video) }}" class="video-card">
        <div class="video-thumb">
            @if($video->thumbnail_path)
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->caption }}">
            @else
                🎬
            @endif
            <span class="status-badge status-{{ $video->status }}">{{ ucfirst(__('messages.student.video_status.' . $video->status)) }}</span>
        </div>
        <div class="video-info">
            <div class="video-title">{{ Str::limit($video->caption, 50) }}</div>
            <div class="video-meta">{{ $video->created_at->diffForHumans() }} · {{ ucfirst($video->learning_style ?? __('messages.student.dashboard.general')) }}</div>
        </div>
    </a>
    @endforeach
</div>
@endif

<!-- Recent Quiz Attempts -->
@if($recent->isNotEmpty())
<div class="section-head">
    <h2>{{ __('messages.student.dashboard.recent_quizzes') }}</h2>
</div>
<div class="panel">
    <table class="recent-table">
        <thead>
            <tr>
                <th>{{ __('messages.student.dashboard.video') }}</th>
                <th>{{ __('messages.student.dashboard.score') }}</th>
                <th>{{ __('messages.student.dashboard.result') }}</th>
                <th>{{ __('messages.student.dashboard.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recent as $attempt)
            <tr>
                <td>{{ Str::limit($attempt->quiz->video->caption ?? __('messages.student.dashboard.na'), 35) }}</td>
                <td><span class="score-pill {{ $attempt->passed ? 'score-pass' : 'score-fail' }}">{{ $attempt->score }}%</span></td>
                <td>{{ $attempt->passed ? __('messages.student.dashboard.passed') : __('messages.student.dashboard.failed') }}</td>
                <td>{{ $attempt->created_at->format('M d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@push('scripts')
<script>
const messages = {
    fillBothFields: '{{ __("messages.student.dashboard.fill_both_fields") }}',
    queuing: '{{ __("messages.student.dashboard.queuing") }}',
    sendingRequest: '{{ __("messages.student.dashboard.sending_request") }}',
    generating: '{{ __("messages.student.dashboard.generating") }}',
    errorPrefix: '{{ __("messages.student.dashboard.error_prefix") }}',
    generate: '{{ __("messages.student.dashboard.generate_button") }}'
};

function requestVideo() {
    const topic   = document.getElementById('genTopic').value.trim();
    const caption = document.getElementById('genCaption').value.trim();
    const btn     = document.getElementById('genBtn');
    const status  = document.getElementById('genStatus');

    if (!topic || !caption) {
        status.style.display = 'block';
        status.style.color   = '#FCA5A5';
        status.textContent   = '⚠️ ' + messages.fillBothFields;
        return;
    }

    btn.classList.add('loading');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + messages.queuing;
    status.style.display = 'block';
    status.style.color   = 'var(--muted)';
    status.textContent   = '🔄 ' + messages.sendingRequest;

    fetch('{{ route("student.videos.request") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ topic, caption }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            status.style.color = '#34D399';
            status.textContent = '✅ ' + data.message;
            document.getElementById('genTopic').value   = '';
            document.getElementById('genCaption').value = '';
            setTimeout(() => window.location.reload(), 2500);
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    })
    .catch(err => {
        status.style.color = '#FCA5A5';
        status.textContent = '❌ ' + messages.errorPrefix + ': ' + err.message;
        btn.classList.remove('loading');
        btn.innerHTML = '<i class="fas fa-magic"></i> ' + messages.generate;
    });
}

// Auto-refresh if there are pending videos
@if($pending > 0)
setTimeout(() => window.location.reload(), 15000);
@endif
</script>
@endpush