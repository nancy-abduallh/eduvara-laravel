@extends('layouts.student')
@section('title', __('messages.student.history.video_title'))
@section('page-title', __('messages.student.history.video_title'))

@section('content')
<a href="{{ route('student.history') }}" style="color:var(--muted);font-size:0.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:1.5rem;">
    ← {{ __('messages.student.history.back_link') }}
</a>

@if($videos->isEmpty())
<div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:4rem;text-align:center;">
    <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
    <p style="color:var(--muted);">{{ __('messages.student.history.no_videos') }}</p>
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
            <div class="video-title">{{ Str::limit($video->caption, 55) }}</div>
            <div class="video-meta" style="margin-top:0.4rem;display:flex;gap:0.8rem;">
                <span>{{ ucfirst($video->learning_style ?? __('messages.student.history.general')) }}</span>
                <span>·</span>
                <span>{{ $video->created_at->format('M d, Y') }}</span>
            </div>
            @if($video->quiz)
                <div style="margin-top:0.6rem;font-size:0.75rem;color:#A78BFA;">
                    📝 {{ __('messages.student.history.quiz_prefix') }}: {{ $video->quiz->status === 'ready' ? __('messages.student.history.quiz_available') : ucfirst(__('messages.student.video_status.' . $video->quiz->status)) }}
                </div>
            @endif
        </div>
    </a>
    @endforeach
</div>
<div style="margin-top:1.5rem;">{{ $videos->links() }}</div>
@endif
@endsection