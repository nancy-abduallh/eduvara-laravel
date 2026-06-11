@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'System Overview')

@section('content')
<!-- Stats Grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">
    @foreach([
        ['Total Users',   $stats['total_users'],    '👥', '#7C3AED'],
        ['Total Videos',  $stats['total_videos'],   '🎬', '#F59E0B'],
        ['Processing',    $stats['processing'],      '⚙️', '#06B6D4'],
        ['Completed',     $stats['completed'],       '✅', '#10B981'],
        ['Failed',        $stats['failed'],          '❌', '#EF4444'],
        ['Quiz Attempts', $stats['quiz_attempts'],   '📝', '#EC4899'],
        ['Avg Score',     round($stats['avg_score']).'%', '🏆', '#A855F7'],
        ['New Today',     $stats['new_users_today'], '🆕', '#F97316'],
    ] as [$label, $val, $icon, $color])
    <div class="admin-stat-card" style="border-top-color:{{ $color }}">
        <div style="font-size:1.5rem;margin-bottom:0.5rem;">{{ $icon }}</div>
        <div style="font-size:1.8rem;font-weight:800;font-family:'Space Grotesk',sans-serif;">{{ $val }}</div>
        <div style="font-size:0.75rem;color:var(--muted);">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
    <!-- Recent Users -->
    <div class="admin-panel">
        <h3>👥 Recent Users</h3>
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Style</th><th>Joined</th></tr></thead>
            <tbody>
                @foreach($recentUsers as $u)
                <tr>
                    <td>
                        <a href="{{ route('admin.users.show', $u) }}" style="color:var(--primary);text-decoration:none;">{{ $u->name }}</a>
                        <div style="font-size:0.72rem;color:var(--muted);">{{ $u->email }}</div>
                    </td>
                    <td>{{ ucfirst($u->learning_style ?? '—') }}</td>
                    <td>{{ $u->created_at->format('M d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.users.index') }}" style="display:block;text-align:center;margin-top:1rem;color:var(--primary);font-size:0.85rem;text-decoration:none;">View All Users →</a>
    </div>

    <!-- Recent Videos -->
    <div class="admin-panel">
        <h3>🎬 Recent Videos</h3>
        <table class="admin-table">
            <thead><tr><th>Caption</th><th>User</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($recentVideos as $v)
                <tr>
                    <td>{{ Str::limit($v->caption, 28) }}</td>
                    <td style="font-size:0.8rem;color:var(--muted);">{{ $v->user->name }}</td>
                    <td><span class="status-badge status-{{ $v->status }}">{{ ucfirst($v->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.videos.index') }}" style="display:block;text-align:center;margin-top:1rem;color:var(--primary);font-size:0.85rem;text-decoration:none;">View All Videos →</a>
    </div>
</div>

<!-- VARK Distribution -->
@if($styleDistribution->isNotEmpty())
<div class="admin-panel" style="margin-top:1.5rem;">
    <h3>🧠 VARK Distribution</h3>
    <div style="display:flex;gap:1.5rem;flex-wrap:wrap;margin-top:1rem;">
        @php
            $colors = ['visual'=>'#7C3AED','auditory'=>'#F59E0B','reading'=>'#10B981','kinesthetic'=>'#EC4899'];
            $total  = $styleDistribution->sum('count');
        @endphp
        @foreach($styleDistribution as $style)
        <div style="flex:1;min-width:120px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;font-size:0.82rem;">
                <span>{{ ucfirst($style->learning_style) }}</span>
                <span style="font-weight:700;">{{ $style->count }}</span>
            </div>
            <div style="background:rgba(255,255,255,0.06);border-radius:50px;height:8px;overflow:hidden;">
                <div style="height:100%;border-radius:50px;background:{{ $colors[$style->learning_style] ?? '#7C3AED' }};width:{{ $total > 0 ? round(($style->count/$total)*100) : 0 }}%;"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
