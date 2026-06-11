@extends('layouts.student')
@section('title', __('messages.student.history.chat_title'))
@section('page-title', __('messages.student.history.chat_title'))

@section('content')
<a href="{{ route('student.history') }}" style="color:var(--muted);font-size:0.85rem;text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;margin-bottom:1.5rem;">
    ← {{ __('messages.student.history.back_link') }}
</a>

@if($chats->isEmpty())
<div style="background:var(--card);border:1px solid var(--border);border-radius:18px;padding:4rem;text-align:center;">
    <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
    <p style="color:var(--muted);">{{ __('messages.student.history.no_chats') }}</p>
</div>
@else
<div style="display:flex;flex-direction:column;gap:1rem;max-width:760px;">
    @php $grouped = $chats->groupBy(fn($c) => $c->created_at->format('M d, Y')); @endphp
    @foreach($grouped as $date => $messages)
        <div style="font-size:0.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:1px;padding:0.5rem 0;">{{ $date }}</div>
        @foreach($messages as $msg)
        <div style="display:flex;gap:1rem;align-items:flex-start;{{ $msg->role === 'user' ? 'flex-direction:row-reverse;' : '' }}">
            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;
                {{ $msg->role === 'user' ? 'background:linear-gradient(135deg,#7C3AED,#9333EA);' : 'background:rgba(255,255,255,0.1);' }}">
                {{ $msg->role === 'user' ? strtoupper(substr(auth()->user()->name, 0, 1)) : '🤖' }}
            </div>
            <div style="max-width:75%;">
                <div style="background:{{ $msg->role === 'user' ? 'linear-gradient(135deg,rgba(124,58,237,0.2),rgba(147,51,234,0.1))' : 'var(--card)' }};
                    border:1px solid var(--border);border-radius:14px;
                    padding:0.8rem 1.1rem;font-size:0.88rem;line-height:1.6;
                    {{ $msg->role === 'user' ? 'border-color:rgba(124,58,237,0.3);' : '' }}">
                    {{ $msg->content }}
                </div>
                <div style="font-size:0.72rem;color:var(--muted);margin-top:0.3rem;
                    {{ $msg->role === 'user' ? 'text-align:right;' : '' }}">
                    {{ $msg->created_at->format('H:i') }}
                    @if($msg->video)
                        · <a href="{{ route('student.videos.show', $msg->video) }}" style="color:var(--primary);text-decoration:none;">{{ Str::limit($msg->video->caption, 25) }}</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    @endforeach
</div>
<div style="margin-top:1.5rem;">{{ $chats->links() }}</div>
@endif
@endsection