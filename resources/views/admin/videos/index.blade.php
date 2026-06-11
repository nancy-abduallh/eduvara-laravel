@extends('layouts.admin')
@section('title', 'Videos')
@section('page-title', 'Video Management')

@section('content')
<div style="display:flex;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <form method="GET" style="display:flex;gap:0.8rem;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search captions..."
            style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:0.65rem 1rem;color:var(--text);font-size:0.88rem;font-family:'Inter',sans-serif;">
        <select name="status" style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:0.65rem 1rem;color:var(--text);font-size:0.85rem;">
            <option value="">All Status</option>
            @foreach(['queued','processing','completed','failed'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:linear-gradient(135deg,#7C3AED,#9333EA);color:#fff;border:none;border-radius:10px;padding:0.65rem 1.2rem;font-weight:600;cursor:pointer;">Filter</button>
    </form>
</div>

<div class="admin-panel" style="padding:0;overflow:hidden;">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="padding:1rem 1.2rem;">Caption</th>
                <th>User</th>
                <th>Status</th>
                <th>Style</th>
                <th>Views</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($videos as $video)
            <tr>
                <td style="padding:1rem 1.2rem;max-width:200px;">{{ Str::limit($video->caption, 40) }}</td>
                <td style="font-size:0.82rem;">{{ $video->user->name }}</td>
                <td><span class="status-badge status-{{ $video->status }}">{{ ucfirst($video->status) }}</span></td>
                <td style="font-size:0.82rem;">{{ ucfirst($video->learning_style ?? '—') }}</td>
                <td>{{ $video->view_count }}</td>
                <td style="font-size:0.78rem;color:var(--muted);">{{ $video->created_at->format('M d, Y') }}</td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ route('admin.videos.show', $video) }}"
                            style="background:rgba(6,182,212,0.2);color:#67E8F9;border:1px solid rgba(6,182,212,0.25);padding:0.3rem 0.8rem;border-radius:8px;text-decoration:none;font-size:0.78rem;font-weight:600;">
                            View
                        </a>
                        <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:rgba(239,68,68,0.15);color:#FCA5A5;border:1px solid rgba(239,68,68,0.25);padding:0.3rem 0.8rem;border-radius:8px;cursor:pointer;font-size:0.78rem;font-weight:600;">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div style="margin-top:1.5rem;">{{ $videos->links() }}</div>
@endsection
