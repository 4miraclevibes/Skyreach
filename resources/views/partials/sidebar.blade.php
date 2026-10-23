<aside class="sidebar">
    <a class="brand" href="{{ route('dashboard') }}">
        @include('partials.wordmark')
        <p>Manajemen Kontrak</p>
    </a>

    <nav class="side-nav" aria-label="Utama">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.4"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.4"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.4"/></svg>
            Dashboard
        </a>
        <a class="nav-link {{ request()->routeIs('contracts.*') ? 'is-active' : '' }}" href="{{ route('contracts.index') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3.5h7.2L19 8.2V20a1.5 1.5 0 0 1-1.5 1.5h-10A1.5 1.5 0 0 1 6 20V5A1.5 1.5 0 0 1 7.5 3.5H7z"/><path d="M14 3.8V8.2H18.4"/></svg>
            Kontrak
        </a>
        <a class="nav-link {{ request()->routeIs('documents.*') ? 'is-active' : '' }}" href="{{ route('documents.index') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8.2h5.2l1.6 1.8H20a1.2 1.2 0 0 1 1.2 1.2v7.2A1.6 1.6 0 0 1 19.6 20H5.4A1.6 1.6 0 0 1 3.8 18.4V9.4A1.2 1.2 0 0 1 5 8.2H4z"/><path d="M4 8.4V6.2A1.2 1.2 0 0 1 5.2 5H10"/></svg>
            Dokumen
        </a>
    </nav>

    <nav class="side-nav side-nav-foot" aria-label="Panduan">
        <a class="nav-link {{ request()->routeIs('guides.*') ? 'is-active' : '' }}" href="{{ route('guides.user') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.5 5.2h5.4A2.6 2.6 0 0 1 13.5 7.8V19H7.6A2.1 2.1 0 0 0 5.5 21.1V5.2z"/><path d="M18.5 5.2h-5.4A2.6 2.6 0 0 0 10.5 7.8V19h5.9a2.1 2.1 0 0 1 2.1 2.1V5.2z"/></svg>
            Panduan
        </a>
    </nav>

    <div class="user-card">
        <div class="avatar" aria-hidden="true">{{ auth()->user()->initials() }}</div>
        <div class="user-meta">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->roleLabel() }}</span>
            <em>{{ auth()->user()->email }}</em>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Logout" title="Logout">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 7V5.8A1.8 1.8 0 0 1 11.8 4h6.4A1.8 1.8 0 0 1 20 5.8v12.4a1.8 1.8 0 0 1-1.8 1.8h-6.4A1.8 1.8 0 0 1 10 18.2V17"/><path d="M13 12H4"/><path d="M7 9l-3 3 3 3"/></svg>
            </button>
        </form>
    </div>
</aside>
