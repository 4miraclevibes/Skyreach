<nav class="guide-switch" aria-label="Jenis panduan">
    <a class="{{ request()->routeIs('guides.user') ? 'is-active' : '' }}" href="{{ route('guides.user') }}">Pemakaian</a>
    <a class="{{ request()->routeIs('guides.technical') ? 'is-active' : '' }}" href="{{ route('guides.technical') }}">Teknis IT</a>
</nav>
