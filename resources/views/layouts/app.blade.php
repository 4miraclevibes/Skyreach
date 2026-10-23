<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Skyreach Contracts')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="@yield('body-class')">
    <div class="shell">
        @include('partials.sidebar')
        <div class="main">
            <header class="topbar">
                <p class="crumb">@yield('crumb', 'Dashboard')</p>
                <div class="topbar-tools">
                    @yield('topbar')
                    <span class="telemetry"><i class="dot"></i> Telemetri aktif</span>
                    <span class="avatar avatar-sm" title="{{ auth()->user()->name }}">{{ auth()->user()->initials() }}</span>
                </div>
            </header>
            <main class="page">
                @if (session('status'))
                    <div class="flash" role="status">{{ session('status') }}</div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
