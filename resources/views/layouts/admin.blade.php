<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control Panel') — Eduvara Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="admin-body">

<aside class="admin-sidebar">
    <div class="admin-logo">
        <span>🛡️ Control Panel</span>
    </div>
    <nav class="admin-nav">
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Dashboard
        </a>
        <a href="{{ route('admin.users.index') }}" class="admin-nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="{{ route('admin.videos.index') }}" class="admin-nav-item {{ request()->routeIs('admin.videos*') ? 'active' : '' }}">
            <i class="fas fa-video"></i> Videos
        </a>
        <a href="{{ route('admin.system') }}" class="admin-nav-item {{ request()->routeIs('admin.system') ? 'active' : '' }}">
            <i class="fas fa-server"></i> System
        </a>
    </nav>
    <div class="admin-sidebar-footer">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-header">
        <h1>@yield('page-title', 'Dashboard')</h1>
    </header>
    @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    <div class="admin-content">
        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
