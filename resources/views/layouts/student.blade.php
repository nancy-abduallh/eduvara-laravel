<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.student.dashboard.title')) — EDUGENIE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* RTL Support for Sidebar */
        [dir="rtl"] .sidebar {
            left: auto;
            right: 0;
        }
        
        [dir="rtl"] .sidebar.collapsed {
            transform: translateX(250px);
        }
        
        [dir="rtl"] .main-content {
            margin-right: 260px;
            margin-left: 0;
        }
        
        [dir="rtl"] .main-content.expanded {
            margin-right: 80px;
        }
        
        [dir="rtl"] .sidebar-toggle {
            margin-right: 0;
            margin-left: 1rem;
        }
        
        [dir="rtl"] .user-info-sm {
            text-align: right;
        }
        
        /* Base Styles */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #7C3AED;
            --secondary: #F59E0B;
            --accent: #10B981;
            --danger: #EF4444;
            --dark: #0D0D1A;
            --darker: #07070F;
            --card: rgba(255,255,255,0.04);
            --text: #F8FAFC;
            --muted: rgba(248,250,252,0.55);
            --border: rgba(255,255,255,0.08);
            --glow: rgba(124,58,237,0.35);
            --sidebar-width: 260px;
            --sidebar-collapsed: 80px;
        }

        body {
            background: var(--darker);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: rgba(7,7,15,0.98);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
        }

        .sidebar.collapsed {
            transform: translateX(-180px);
        }

        .sidebar.collapsed .logo-text,
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .user-info-sm {
            display: none;
        }

        .sidebar.collapsed .sidebar-user {
            justify-content: center;
            padding: 1rem;
        }

        .sidebar-logo {
            padding: 1.5rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .logo-icon {
            font-size: 1.8rem;
        }

        .logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            color: var(--muted);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.25s;
        }

        .nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        .nav-item span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .nav-item:hover {
            background: rgba(124,58,237,0.1);
            color: var(--text);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(147,51,234,0.1));
            color: #A78BFA;
            border-left: 3px solid var(--primary);
        }

        [dir="rtl"] .nav-item.active {
            border-left: none;
            border-right: 3px solid var(--primary);
        }

        .sidebar-user {
            padding: 1rem 1.5rem 2rem;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar-sm {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), #9333EA);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        .user-info-sm {
            flex: 1;
        }

        .user-name-sm {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .user-style-sm {
            font-size: 0.7rem;
            color: var(--muted);
        }

        .logout-btn {
            background: rgba(239,68,68,0.15);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 0.5rem;
            color: #FCA5A5;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(239,68,68,0.25);
            color: #FECACA;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        .topbar {
            background: rgba(7,7,15,0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .sidebar-toggle {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            border-color: var(--primary);
            background: rgba(124,58,237,0.1);
        }

        .topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--muted);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .vark-badge {
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 30px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Flash Messages */
        .flash-success {
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            margin: 1rem 2rem;
            color: #34D399;
        }

        .flash-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            margin: 1rem 2rem;
            color: #FCA5A5;
        }

        .page-content {
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show-mobile {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .page-content {
                padding: 1rem;
            }
            .flash-success, .flash-error {
                margin: 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="student-body">

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">⚡</span>
        <span class="logo-text">EDUGENIE</span>
    </div>

    <nav class="sidebar-nav">
        <!-- home button route home-->
        <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>{{ __('messages.student.nav.home') }}</span>
        </a>
        <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>{{ __('messages.student.nav.dashboard') }}</span>
        </a>
        <a href="{{ route('student.upload') }}" class="nav-item {{ request()->routeIs('student.upload*') ? 'active' : '' }}">
            <i class="fas fa-cloud-upload-alt"></i><span>{{ __('messages.student.nav.upload') }}</span>
        </a>
        <a href="{{ route('student.videos') }}" class="nav-item {{ request()->routeIs('student.videos*') ? 'active' : '' }}">
            <i class="fas fa-film"></i><span>{{ __('messages.student.nav.videos') }}</span>
        </a>
        <a href="{{ route('student.history.videos') }}" class="nav-item {{ request()->routeIs('student.history*') ? 'active' : '' }}">
            <i class="fas fa-history"></i><span>{{ __('messages.student.nav.history') }}</span>
        </a>
    </nav>

    <div class="sidebar-user">
        <div class="user-avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="user-info-sm">
            <div class="user-name-sm">{{ auth()->user()->name }}</div>
            <div class="user-style-sm">{{ ucfirst(auth()->user()->learning_style ?? __('messages.student.nav.not_set')) }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn" title="{{ __('messages.student.nav.logout') }}"><i class="fas fa-sign-out-alt"></i></button>
        </form>
    </div>
</aside>

<!-- Main -->
<main class="main-content" id="main-content">
    <!-- Top Bar -->
    <header class="topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <div class="topbar-title">@yield('page-title', __('messages.student.nav.dashboard'))</div>
        <div class="topbar-actions">
            <div class="vark-badge">
                <i class="fas fa-brain"></i>
                {{ ucfirst(auth()->user()->learning_style ?? __('messages.student.nav.assessing')) }}
            </div>
        </div>
    </header>

    <!-- Flash -->
    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">{{ session('error') }}</div>
    @endif

    <div class="page-content">
        @yield('content')
    </div>
</main>

@stack('scripts')
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('show-mobile');
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (window.innerWidth <= 768 && 
        sidebar.classList.contains('show-mobile') &&
        !sidebar.contains(event.target) &&
        !toggleBtn.contains(event.target)) {
        sidebar.classList.remove('show-mobile');
    }
});

// Handle window resize
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    
    if (window.innerWidth > 768) {
        sidebar.classList.remove('show-mobile');
    }
});
</script>
</body>
</html>