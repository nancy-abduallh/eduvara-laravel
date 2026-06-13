@extends('layouts.student')
@section('title', $video->caption)
@section('page-title', Str::limit($video->caption, 40))

@push('styles')
<style>
.video-show-grid {
    display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem;
}
@media(max-width:900px) { .video-show-grid { grid-template-columns: 1fr; } }

/* RTL Support */
[dir="rtl"] .chat-msg.user {
    align-self: flex-start;
    border-bottom-right-radius: 14px;
    border-bottom-left-radius: 4px;
}
[dir="rtl"] .chat-msg.assistant {
    align-self: flex-end;
    border-bottom-left-radius: 14px;
    border-bottom-right-radius: 4px;
}

.video-player-panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 20px; overflow: hidden;
}
.video-player-wrap {
    position: relative; padding-bottom: 56.25%; background: #000;
}
.video-player-wrap video, .video-placeholder {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
}
.video-placeholder {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(245,158,11,0.1));
    color: var(--muted);
}
.processing-spinner {
    width: 60px; height: 60px; border: 3px solid rgba(255,255,255,0.1);
    border-top-color: var(--primary); border-radius: 50%;
    animation: spin 1s linear infinite; margin-bottom: 1rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
.video-details { padding: 1.5rem; }
.video-title-full { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.8rem; }
.video-tags { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; }
.tag {
    padding: 0.3rem 0.8rem; border-radius: 50px;
    font-size: 0.75rem; font-weight: 600;
    background: rgba(124,58,237,0.15);
    color: #A78BFA; border: 1px solid rgba(124,58,237,0.25);
}

/* Sidebar panels */
.side-panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 20px; padding: 1.5rem; margin-bottom: 1.2rem;
}
.side-panel h3 { font-size: 0.95rem; font-weight: 700; margin-bottom: 1.2rem; }
.btn-quiz {
    display: block; width: 100%;
    background: linear-gradient(135deg, #F59E0B, #EF4444);
    color: #fff; border: none; border-radius: 12px;
    padding: 0.9rem; font-weight: 700; font-size: 0.95rem;
    text-align: center; text-decoration: none;
    cursor: pointer; transition: all 0.25s;
    box-shadow: 0 0 20px rgba(245,158,11,0.3);
}
.btn-quiz:hover { transform: translateY(-2px); box-shadow: 0 0 35px rgba(245,158,11,0.4); }
.btn-quiz.disabled { opacity: 0.4; pointer-events: none; }

/* Chat History */
.chat-log {
    max-height: 300px; overflow-y: auto;
    display: flex; flex-direction: column; gap: 0.8rem;
    margin-bottom: 1rem;
}
.chat-log::-webkit-scrollbar { width: 4px; }
.chat-log::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
.chat-msg {
    max-width: 85%; padding: 0.7rem 1rem;
    border-radius: 14px; font-size: 0.88rem; line-height: 1.5;
}
.chat-msg.user   { align-self: flex-end; background: linear-gradient(135deg, #7C3AED, #9333EA); color: #fff; border-bottom-right-radius: 4px; }
.chat-msg.assistant { align-self: flex-start; background: rgba(255,255,255,0.07); border: 1px solid var(--border); border-bottom-left-radius: 4px; }
.chat-input-row { display: flex; gap: 0.6rem; }
.chat-text-input {
    flex: 1; background: rgba(255,255,255,0.06);
    border: 1px solid var(--border); border-radius: 10px;
    padding: 0.6rem 0.9rem; color: var(--text); font-size: 0.85rem;
    font-family: 'Inter', sans-serif;
}
.chat-text-input:focus { outline: none; border-color: var(--primary); }
.chat-send {
    background: var(--primary); border: none; border-radius: 10px;
    padding: 0.6rem 0.9rem; color: #fff; cursor: pointer;
    font-size: 0.9rem; transition: background 0.2s;
}
.chat-send:hover { background: #9333EA; }
</style>
@endpush

@section('content')
<div class="video-show-grid">

    <!-- Left: Video + Info -->
    <div>
        <div class="video-player-panel">
            <div class="video-player-wrap">
                @if($video->isReady() && $video->video_path)
                    <video controls>
                        <source src="{{ $video->video_url }}" type="video/mp4">
                        {{ __('messages.student.videoshow.browser_support') }}
                    </video>
                @else
                    <div class="video-placeholder">
                        <div class="processing-spinner"></div>
                        <p>{{ ucfirst(__('messages.student.videoshow.status_' . $video->status)) }}...</p>
                        <small>{{ $video->status === 'queued' ? __('messages.student.videoshow.waiting_queue') : __('messages.student.videoshow.generating') }}</small>
                    </div>
                @endif
            </div>
            <div class="video-details">
                <div class="video-title-full">{{ $video->caption }}</div>
                <div class="video-tags">
                    <span class="tag">{{ ucfirst($video->learning_style ?? __('messages.student.videoshow.general')) }}</span>
                    <span class="tag">{{ ucfirst($video->proficiency_level ?? __('messages.student.videoshow.beginner')) }}</span>
                    <span class="tag">{{ strtoupper($video->language) }}</span>
                    @if($video->duration_seconds)
                        <span class="tag">{{ gmdate('i:s', $video->duration_seconds) }}</span>
                    @endif
                </div>
                @if($video->topic)
                    <p style="color:var(--muted);font-size:0.88rem;line-height:1.7;">{{ $video->topic }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Sidebar -->
    <div>

        <!-- Quiz Panel -->
        <div class="side-panel">
            <h3>{{ __('messages.student.videoshow.quiz_title') }}</h3>
            @if($quiz && $quiz->status === 'ready')
                <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">{{ __('messages.student.videoshow.questions_ready', ['count' => $quiz->questions->count()]) }}</p>
                <a href="{{ route('student.quiz.show', $video) }}" class="btn-quiz">
                    🧠 {{ __('messages.student.videoshow.take_quiz') }}
                </a>
            @elseif($video->isReady())
                <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">{{ __('messages.student.videoshow.quiz_generating') }}</p>
                <span class="btn-quiz disabled">⏳ {{ __('messages.student.videoshow.quiz_generating_button') }}</span>
            @else
                <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">{{ __('messages.student.videoshow.quiz_waiting') }}</p>
                <span class="btn-quiz disabled">🔒 {{ __('messages.student.videoshow.waiting_video') }}</span>
            @endif
        </div>

        <!-- Chat / History -->
        <div class="side-panel">
            <h3>{{ __('messages.student.videoshow.chat_title') }}</h3>
            <div class="chat-log" id="chatLog">
                @forelse($chatHistory as $msg)
                    <div class="chat-msg {{ $msg->role }}">{{ $msg->content }}</div>
                @empty
                    <p style="color:var(--muted);font-size:0.82rem;text-align:center;">{{ __('messages.student.videoshow.no_messages') }}</p>
                @endforelse
            </div>
            <div class="chat-input-row">
                <input type="text" class="chat-text-input" id="chatInput"
                    placeholder="{{ __('messages.student.videoshow.chat_placeholder') }}"
                    onkeydown="if(event.key==='Enter') sendChat()">
                <button class="chat-send" onclick="sendChat()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>

        <!-- Video Info Panel -->
        <div class="side-panel">
            <h3>{{ __('messages.student.videoshow.info_title') }}</h3>
            <div style="display:flex;flex-direction:column;gap:0.7rem;font-size:0.85rem;">
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--muted)">{{ __('messages.student.videoshow.status_label') }}</span>
                    <span class="status-badge status-{{ $video->status }}">{{ ucfirst(__('messages.student.video_status.' . $video->status)) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--muted)">{{ __('messages.student.videoshow.views_label') }}</span>
                    <span>{{ $video->view_count }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:var(--muted)">{{ __('messages.student.videoshow.created_label') }}</span>
                    <span>{{ $video->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-poll if processing
@if(!$video->isReady())
const pollInterval = setInterval(() => {
    fetch('{{ route("student.videos.status", $video) }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.ready) {
            clearInterval(pollInterval);
            window.location.reload();
        }
    });
}, 8000);
@endif

// ── Chat ──────────────────────────────────────────────────────────────────
const CHAT_SEND_URL = '{{ route("student.chat.send") }}';
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]').content;
const VIDEO_ID      = {{ $video->id }};

async function sendChat() {
    const input   = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;

    const log = document.getElementById('chatLog');

    // Remove the "no messages" placeholder if present
    const empty = log.querySelector('p');
    if (empty) empty.remove();

    // Append user bubble immediately
    appendBubble(log, 'user', message);
    input.value = '';

    // Typing indicator
    const typingId = 'typing-' + Date.now();
    appendBubble(log, 'assistant', '…', typingId);

    try {
        const res = await fetch(CHAT_SEND_URL, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ video_id: VIDEO_ID, message }),
        });

        const data = await res.json();

        // Replace typing indicator with real reply
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.textContent = data.reply ?? data.error ?? 'Error';

    } catch (err) {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.textContent = 'Connection error. Please try again.';
    }

    log.scrollTop = log.scrollHeight;
}

function appendBubble(log, role, text, id = null) {
    const div = document.createElement('div');
    div.className = 'chat-msg ' + role;
    div.textContent = text;
    if (id) div.id = id;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
}

// Send on Enter key
document.getElementById('chatInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendChat(); }
});
</script>
@endpush