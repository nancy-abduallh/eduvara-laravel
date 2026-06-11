@extends('layouts.admin')
@section('title', 'System Status')
@section('page-title', 'System Monitoring')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    <!-- Queue Status -->
    <div class="admin-panel">
        <h3>⚙️ Queue Status</h3>
        <div style="display:flex;flex-direction:column;gap:0.8rem;margin-top:0.5rem;">
            @php
                $queued     = \App\Models\Video::where('status','queued')->count();
                $processing = \App\Models\Video::where('status','processing')->count();
                $failed     = \App\Models\Video::where('status','failed')->count();
                $completed  = \App\Models\Video::where('status','completed')->count();
            @endphp
            @foreach([
                ['Queued',     $queued,     '#A5B4FC', '#3730A3'],
                ['Processing', $processing, '#FCD34D', '#92400E'],
                ['Completed',  $completed,  '#34D399', '#065F46'],
                ['Failed',     $failed,     '#FCA5A5', '#7F1D1D'],
            ] as [$label, $val, $color, $bg])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.8rem 1rem;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--border);">
                <span style="font-size:0.88rem;">{{ $label }} Videos</span>
                <span style="font-weight:800;font-size:1.1rem;color:{{ $color }};">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        <div style="margin-top:1.2rem;padding:0.9rem;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:10px;font-size:0.82rem;color:#FCD34D;">
            ⚠️ Queue worker must be running: <code style="background:rgba(0,0,0,0.3);padding:0.1rem 0.4rem;border-radius:4px;">php artisan queue:work database</code>
        </div>
    </div>

    <!-- AI Backend Status -->
    <div class="admin-panel">
        <h3>🤖 AI Backend</h3>
        <div style="display:flex;flex-direction:column;gap:0.8rem;margin-top:0.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.8rem 1rem;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--border);">
                <span style="font-size:0.85rem;">AI API URL</span>
                <code style="font-size:0.75rem;color:#A78BFA;">{{ config('services.ai.url') }}</code>
            </div>
            <div id="aiStatusRow" style="display:flex;justify-content:space-between;align-items:center;padding:0.8rem 1rem;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--border);">
                <span style="font-size:0.85rem;">Connection</span>
                <span id="aiStatus" style="font-size:0.82rem;color:#FCD34D;">🔄 Checking...</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.8rem 1rem;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid var(--border);">
                <span style="font-size:0.85rem;">Webhook Secret</span>
                <span style="color:#34D399;font-size:0.82rem;">{{ config('services.ai.webhook_secret') ? '✅ Set' : '❌ Not Set' }}</span>
            </div>
        </div>
    </div>

    <!-- Database Stats -->
    <div class="admin-panel">
        <h3>🗄️ Database Overview</h3>
        <div style="display:flex;flex-direction:column;gap:0.6rem;margin-top:0.5rem;">
            @foreach([
                ['Users',           \App\Models\User::count()],
                ['Videos',          \App\Models\Video::count()],
                ['Quizzes',         \App\Models\Quiz::count()],
                ['Quiz Attempts',   \App\Models\QuizAttempt::count()],
                ['Uploads',         \App\Models\Upload::count()],
                ['VARK Assessments',\App\Models\VarkAssessment::count()],
                ['Adaptive Lessons',\App\Models\AdaptiveLesson::count()],
                ['Chat Messages',   \App\Models\ChatHistory::count()],
            ] as [$label, $val])
            <div style="display:flex;justify-content:space-between;font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                <span style="color:var(--muted);">{{ $label }}</span>
                <span style="font-weight:700;">{{ number_format($val) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- PHP / Laravel Info -->
    <div class="admin-panel">
        <h3>🖥️ Environment</h3>
        <div style="display:flex;flex-direction:column;gap:0.6rem;margin-top:0.5rem;">
            @foreach([
                ['PHP Version',    PHP_VERSION],
                ['Laravel',        app()->version()],
                ['Environment',    app()->environment()],
                ['Queue Driver',   config('queue.default')],
                ['Cache Driver',   config('cache.default')],
                ['App Locale',     config('app.locale')],
                ['Storage',        disk_free_space('/') ? round(disk_free_space('/')/1073741824,1).' GB free' : 'N/A'],
            ] as [$label, $val])
            <div style="display:flex;justify-content:space-between;font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                <span style="color:var(--muted);">{{ $label }}</span>
                <code style="font-size:0.78rem;color:#A78BFA;">{{ $val }}</code>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-panel" style="margin-top:1.5rem;">
    <h3>🚀 Quick Actions</h3>
    <div style="display:flex;gap:0.8rem;flex-wrap:wrap;margin-top:0.8rem;">
        @foreach([
            ['Clear Cache',      'php artisan cache:clear',      '#7C3AED'],
            ['Clear Config',     'php artisan config:clear',     '#F59E0B'],
            ['Clear Views',      'php artisan view:clear',       '#10B981'],
            ['Queue: Restart',   'php artisan queue:restart',    '#EC4899'],
        ] as [$label, $cmd, $color])
        <div style="background:rgba(255,255,255,0.04);border:1px solid var(--border);border-radius:12px;padding:1rem;min-width:180px;">
            <div style="font-weight:600;font-size:0.85rem;margin-bottom:0.4rem;">{{ $label }}</div>
            <code style="font-size:0.72rem;color:#A78BFA;display:block;background:rgba(0,0,0,0.3);padding:0.3rem 0.6rem;border-radius:6px;">{{ $cmd }}</code>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
// Check AI backend connectivity
fetch('{{ config("services.ai.url") }}/health', { signal: AbortSignal.timeout(5000) })
    .then(r => {
        document.getElementById('aiStatus').textContent = r.ok ? '✅ Connected' : '⚠️ Error ' + r.status;
        document.getElementById('aiStatus').style.color = r.ok ? '#34D399' : '#FCD34D';
    })
    .catch(() => {
        document.getElementById('aiStatus').textContent = '❌ Unreachable (Model not running yet)';
        document.getElementById('aiStatus').style.color = '#FCA5A5';
    });
</script>
@endpush
