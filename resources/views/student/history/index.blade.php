@extends('layouts.student')
@section('title', __('messages.student.history.title'))
@section('page-title', __('messages.student.history.page_title'))

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:700px;">
    <a href="{{ route('student.history.videos') }}" style="text-decoration:none;">
        <div style="background:linear-gradient(135deg,rgba(124,58,237,0.2),rgba(147,51,234,0.1));border:1px solid rgba(124,58,237,0.3);border-radius:20px;padding:2.5rem;text-align:center;transition:transform 0.3s,box-shadow 0.3s;color:var(--text);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div style="font-size:3rem;margin-bottom:1rem;">🎬</div>
            <div style="font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">{{ __('messages.student.history.video_title') }}</div>
            <div style="color:var(--muted);font-size:0.85rem;">{{ __('messages.student.history.video_subtitle') }}</div>
        </div>
    </a>
    <a href="{{ route('student.history.chats') }}" style="text-decoration:none;">
        <div style="background:linear-gradient(135deg,rgba(245,158,11,0.2),rgba(239,68,68,0.1));border:1px solid rgba(245,158,11,0.3);border-radius:20px;padding:2.5rem;text-align:center;transition:transform 0.3s;color:var(--text);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div style="font-size:3rem;margin-bottom:1rem;">💬</div>
            <div style="font-weight:700;font-size:1.1rem;margin-bottom:0.5rem;">{{ __('messages.student.history.chat_title') }}</div>
            <div style="color:var(--muted);font-size:0.85rem;">{{ __('messages.student.history.chat_subtitle') }}</div>
        </div>
    </a>
</div>
@endsection