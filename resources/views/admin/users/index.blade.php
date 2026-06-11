@extends('layouts.admin')
@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div style="margin-bottom:1.5rem;display:flex;gap:1rem;align-items:center;">
    <input type="text" placeholder="Search users..." id="userSearch" onkeyup="filterTable()"
        style="background:var(--card);border:1px solid var(--border);border-radius:10px;padding:0.65rem 1rem;color:var(--text);font-family:'Inter',sans-serif;font-size:0.88rem;flex:1;max-width:320px;">
    <span style="color:var(--muted);font-size:0.85rem;">{{ $users->total() }} total users</span>
</div>

<div class="admin-panel" style="padding:0;overflow:hidden;">
    <table class="admin-table" id="userTable">
        <thead>
            <tr>
                <th style="padding:1rem 1.2rem;">User</th>
                <th>Learning Style</th>
                <th>Videos</th>
                <th>Quizzes</th>
                <th>Onboarding</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                <td style="padding:1rem 1.2rem;">
                    <div style="display:flex;align-items:center;gap:0.8rem;">
                        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#9333EA);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.85rem;flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:0.9rem;">{{ $user->name }}</div>
                            <div style="font-size:0.75rem;color:var(--muted);">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ ucfirst($user->learning_style ?? '—') }}</td>
                <td>{{ $user->videos_count }}</td>
                <td>{{ $user->quiz_attempts_count }}</td>
                <td>
                    @if($user->onboarding_completed)
                        <span style="color:#34D399;font-size:0.8rem;">✅ Done</span>
                    @else
                        <span style="color:#FCA5A5;font-size:0.8rem;">⏳ Pending</span>
                    @endif
                </td>
                <td style="font-size:0.82rem;color:var(--muted);">{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                    <div style="display:flex;gap:0.5rem;">
                        <a href="{{ route('admin.users.show', $user) }}"
                            style="background:rgba(124,58,237,0.2);color:#A78BFA;border:1px solid rgba(124,58,237,0.25);padding:0.3rem 0.8rem;border-radius:8px;text-decoration:none;font-size:0.78rem;font-weight:600;">
                            View
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
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
<div style="margin-top:1.5rem;">{{ $users->links() }}</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    const q = document.getElementById('userSearch').value.toLowerCase();
    document.querySelectorAll('#userTable tbody tr').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
