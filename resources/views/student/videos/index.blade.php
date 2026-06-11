@extends('layouts.student')
@section('title', __('messages.student.videos.title'))
@section('page-title', __('messages.student.videos.title'))

@section('content')
<div style="margin-bottom:1.5rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
    <div style="flex:1;min-width:200px;position:relative;">
        <span style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);opacity:0.4;pointer-events:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input type="text" id="searchInput" placeholder="{{ __('messages.student.videos.search_placeholder') }}" onkeyup="filterVideos()"
            style="width:100%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.75rem 1.2rem 0.75rem 2.8rem;color:var(--text);font-size:0.9rem;font-family:'Inter',sans-serif;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
            onfocus="this.style.borderColor='rgba(124,58,237,0.6)'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
    </div>

    {{-- Custom Dropdown --}}
    <div style="position:relative;" id="customDropdown">
        <button onclick="toggleStatusDropdown()" id="dropdownBtn"
            style="display:flex;align-items:center;gap:0.6rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:0.75rem 1rem;color:var(--text);font-size:0.88rem;font-family:'Inter',sans-serif;cursor:pointer;min-width:160px;justify-content:space-between;transition:all 0.2s;outline:none;">
            <span id="dropdownLabel">{{ __('messages.student.videos.all_status') }}</span>
            <svg id="dropdownArrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="opacity:0.5;transition:transform 0.2s;flex-shrink:0;">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>

        <div id="statusDropdownMenu"
            style="display:none;position:absolute;top:calc(100% + 6px);left:0;min-width:100%;background:#13131f;border:1px solid rgba(124,58,237,0.3);border-radius:12px;overflow:hidden;z-index:999;box-shadow:0 8px 32px rgba(0,0,0,0.4);">

            @foreach([
                '' => __('messages.student.videos.all_status'),
                'completed' => __('messages.student.videos.completed_status'),
                'processing' => __('messages.student.videos.processing_status'),
                'queued' => __('messages.student.videos.queued_status'),
                'failed' => __('messages.student.videos.failed_status'),
            ] as $value => $label)
            <div class="status-option" data-value="{{ $value }}"
                onclick="selectStatus('{{ $value }}', '{{ $label }}')"
                style="padding:0.65rem 1rem;font-size:0.88rem;font-family:'Inter',sans-serif;color:rgba(255,255,255,0.75);cursor:pointer;transition:all 0.15s;display:flex;align-items:center;gap:0.5rem;"
                onmouseenter="this.style.background='rgba(124,58,237,0.15)';this.style.color='#fff'"
                onmouseleave="this.style.background='transparent';this.style.color='rgba(255,255,255,0.75)'">
                <svg id="check-{{ $value }}" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="3" style="opacity:0;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $label }}
            </div>
            @endforeach
        </div>
    </div>

    {{-- Hidden native select for filterVideos() compatibility --}}
    <select id="filterStatus" onchange="filterVideos()" style="display:none;">
        <option value="">{{ __('messages.student.videos.all_status') }}</option>
        <option value="completed">{{ __('messages.student.videos.completed_status') }}</option>
        <option value="processing">{{ __('messages.student.videos.processing_status') }}</option>
        <option value="queued">{{ __('messages.student.videos.queued_status') }}</option>
        <option value="failed">{{ __('messages.student.videos.failed_status') }}</option>
    </select>
</div>


@if($videos->isEmpty())
<div class="panel empty-state" style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:4rem 2rem;text-align:center;">
    <div style="font-size:4rem;margin-bottom:1rem;">🎬</div>
    <h3 style="margin-bottom:0.8rem;">{{ __('messages.student.videos.no_videos_title') }}</h3>
    <p style="color:var(--muted);margin-bottom:2rem;">{{ __('messages.student.videos.no_videos_message') }}</p>
    <a href="{{ route('student.dashboard') }}" style="display:inline-block;background:linear-gradient(135deg,#7C3AED,#9333EA);color:#fff;padding:0.8rem 2rem;border-radius:50px;text-decoration:none;font-weight:600;">{{ __('messages.student.videos.go_to_dashboard') }}</a>
</div>
@else
<div class="videos-grid" id="videosGrid">
    @foreach($videos as $video)
    <a href="{{ route('student.videos.show', $video) }}"
        class="video-card"
        data-title="{{ strtolower($video->caption) }}"
        data-status="{{ $video->status }}">
        <div class="video-thumb">
            @if($video->thumbnail_path)
                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->caption }}">
            @else
                <span style="font-size:2.5rem;">🎬</span>
            @endif
            <span class="status-badge status-{{ $video->status }}">{{ ucfirst(__('messages.student.video_status.' . $video->status)) }}</span>
        </div>
        <div class="video-info">
            <div class="video-title">{{ Str::limit($video->caption, 55) }}</div>
            <div class="video-meta" style="display:flex;gap:0.8rem;margin-top:0.5rem;">
                <span>{{ ucfirst($video->learning_style ?? __('messages.student.videos.general_style')) }}</span>
                <span>·</span>
                <span>{{ $video->created_at->format('M d, Y') }}</span>
                @if($video->duration_seconds)
                    <span>· {{ gmdate('i:s', $video->duration_seconds) }}</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>
<div style="margin-top:1.5rem;">{{ $videos->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
function filterVideos() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('filterStatus').value;
    document.querySelectorAll('.video-card').forEach(card => {
        const titleMatch  = card.dataset.title.includes(q);
        const statusMatch = !status || card.dataset.status === status;
        card.style.display = titleMatch && statusMatch ? 'block' : 'none';
    });
}
</script>


<script>
function toggleStatusDropdown() {
    const menu = document.getElementById('statusDropdownMenu');
    const arrow = document.getElementById('dropdownArrow');
    const btn = document.getElementById('dropdownBtn');
    const isOpen = menu.style.display === 'block';
    menu.style.display = isOpen ? 'none' : 'block';
    arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    btn.style.borderColor = isOpen ? 'rgba(255,255,255,0.1)' : 'rgba(124,58,237,0.6)';
}

function selectStatus(value, label) {
    document.getElementById('dropdownLabel').textContent = label;
    document.getElementById('filterStatus').value = value;

    document.querySelectorAll('.status-option').forEach(opt => {
        const check = document.getElementById('check-' + opt.dataset.value);
        if (check) check.style.opacity = opt.dataset.value === value ? '1' : '0';
    });

    toggleStatusDropdown();
    filterVideos();
}

document.addEventListener('click', function(e) {
    if (!document.getElementById('customDropdown').contains(e.target)) {
        document.getElementById('statusDropdownMenu').style.display = 'none';
        document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
        document.getElementById('dropdownBtn').style.borderColor = 'rgba(255,255,255,0.1)';
    }
});
</script>
@endpush