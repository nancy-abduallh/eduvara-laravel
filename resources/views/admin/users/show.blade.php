@extends('layouts.admin')
@section('title', 'User: ' . $user->name)
@section('page-title', 'User Profile')

@section('content')
<a href="{{ route('admin.users.index') }}"
   style="display:inline-flex;align-items:center;gap:0.4rem;color:var(--muted);font-size:0.85rem;text-decoration:none;margin-bottom:1.5rem;">
    ← Back to Users
</a>

<div style="display:grid;grid-template-columns:320px 1fr;gap:1.5rem;">

    <!-- User Card -->
    <div>
        <div class="admin-panel" style="text-align:center;margin-bottom:1.2rem;">
            <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#9333EA);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;margin:0 auto 1rem;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div style="font-size:1.2rem;font-weight:700;margin-bottom:0.3rem;">{{ $user->name }}</div>
            <div style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">{{ $user->email }}</div>
            <span class="status-badge" style="background:rgba(124,58,237,0.15);color:#A78BFA;border:1px solid rgba(124,58,237,0.25);">
                {{ ucfirst($user->role) }}
            </span>

            <div style="margin-top:1.5rem;display:flex;flex-direction:column;gap:0.6rem;text-align:left;">
                @foreach([
                    ['Learning Style', ucfirst($user->learning_style ?? '—')],
                    ['Proficiency',    ucfirst($user->proficiency_level ?? '—')],
                    ['Language',       strtoupper($user->language_preference ?? 'EN')],
                    ['Onboarding',     $user->onboarding_completed ? '✅ Complete' : '⏳ Pending'],
                    ['Joined',         $user->created_at->format('M d, Y')],
                ] as [$label, $value])
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--muted);">{{ $label }}</span>
                    <span style="font-weight:600;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Edit Form -->
        <div class="admin-panel">
            <h3 style="margin-bottom:1rem;">✏️ Edit User</h3>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')
                <div style="margin-bottom:0.8rem;">
                    <label style="font-size:0.75rem;color:var(--muted);font-weight:600;display:block;margin-bottom:0.4rem;">ROLE</label>
                    <select name="role" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:10px;padding:0.6rem 0.9rem;color:var(--text);font-family:'Inter',sans-serif;font-size:0.88rem;">
                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div style="margin-bottom:0.8rem;">
                    <label style="font-size:0.75rem;color:var(--muted);font-weight:600;display:block;margin-bottom:0.4rem;">LEARNING STYLE</label>
                    <select name="learning_style" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:10px;padding:0.6rem 0.9rem;color:var(--text);font-family:'Inter',sans-serif;font-size:0.88rem;">
                        <option value="">— Not Set —</option>
                        @foreach(['visual','auditory','reading','kinesthetic'] as $s)
                            <option value="{{ $s }}" {{ $user->learning_style === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:1rem;">
                    <label style="font-size:0.75rem;color:var(--muted);font-weight:600;display:block;margin-bottom:0.4rem;">PROFICIENCY</label>
                    <select name="proficiency_level" style="width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:10px;padding:0.6rem 0.9rem;color:var(--text);font-family:'Inter',sans-serif;font-size:0.88rem;">
                        <option value="">— Not Set —</option>
                        @foreach(['beginner','intermediate','advanced'] as $p)
                            <option value="{{ $p }}" {{ $user->proficiency_level === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="width:100%;background:linear-gradient(135deg,#7C3AED,#9333EA);color:#fff;border:none;border-radius:10px;padding:0.75rem;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Activity -->
    <div>
        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.2rem;">
            @foreach([
                ['Videos',  $user->videos->count(),       '#7C3AED'],
                ['Quizzes', $user->quizAttempts->count(),  '#F59E0B'],
                ['VARK',    $user->varkAssessments->count(),'#10B981'],
            ] as [$label, $val, $color])
            <div class="admin-stat-card" style="border-top-color:{{ $color }};text-align:center;">
                <div style="font-size:1.8rem;font-weight:800;font-family:'Space Grotesk',sans-serif;">{{ $val }}</div>
                <div style="font-size:0.75rem;color:var(--muted);">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        <!-- VARK Assessment -->
        @if($user->latestVark)
        <div class="admin-panel" style="margin-bottom:1.2rem;">
            <h3>🧠 VARK Assessment Result</h3>
            @php
                $vark   = $user->latestVark;
                $max    = max($vark->visual_score,$vark->auditory_score,$vark->reading_score,$vark->kinesthetic_score) ?: 1;
                $colors = ['visual'=>'#7C3AED','auditory'=>'#F59E0B','reading'=>'#10B981','kinesthetic'=>'#EC4899'];
            @endphp
            <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                <span style="background:rgba(124,58,237,0.15);color:#A78BFA;border:1px solid rgba(124,58,237,0.25);padding:0.3rem 1rem;border-radius:50px;font-weight:700;font-size:0.85rem;">
                    Result: {{ ucfirst($vark->result) }}
                </span>
                <span style="font-size:0.78rem;color:var(--muted);align-self:center;">Taken {{ $vark->created_at->diffForHumans() }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:0.7rem;">
                @foreach(['visual','auditory','reading','kinesthetic'] as $style)
                    @php $score = $vark->{$style.'_score'}; $pct = round(($score/$max)*100); @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;font-size:0.8rem;margin-bottom:0.3rem;">
                            <span>{{ ucfirst($style) }}</span>
                            <span style="font-weight:700;">{{ $score }}</span>
                        </div>
                        <div style="background:rgba(255,255,255,0.06);border-radius:50px;height:7px;overflow:hidden;">
                            <div style="height:100%;border-radius:50px;background:{{ $colors[$style] }};width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Videos -->
        <div class="admin-panel">
            <h3>🎬 Recent Videos</h3>
            @if($user->videos->isEmpty())
                <p style="color:var(--muted);font-size:0.85rem;">No videos generated yet.</p>
            @else
            <table class="admin-table">
                <thead><tr><th>Caption</th><th>Status</th><th>Style</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($user->videos->take(8) as $video)
                    <tr>
                        <td>{{ Str::limit($video->caption, 35) }}</td>
                        <td><span class="status-badge status-{{ $video->status }}">{{ ucfirst($video->status) }}</span></td>
                        <td style="font-size:0.78rem;color:var(--muted);">{{ ucfirst($video->learning_style ?? '—') }}</td>
                        <td style="font-size:0.78rem;color:var(--muted);">{{ $video->created_at->format('M d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
