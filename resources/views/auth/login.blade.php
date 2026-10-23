<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Skyreach Contracts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="login">
        <section class="login-hero">
            @include('partials.wordmark')
            <p class="login-kicker">Manajemen Kontrak</p>
            <span class="tag">Everywhere Excellence</span>
            <h1>Infrastruktur transmisi dan tata kelola kontrak</h1>
            <p class="story">Manajemen kontrak internal untuk jaringan VSAT di seluruh Nusantara.</p>
            <svg class="constellation" viewBox="0 0 420 180" aria-hidden="true">
                <g fill="none" stroke="#EFA646" stroke-width="1" opacity="0.7">
                    <path d="M20 140 L90 90 L150 120 L220 40 L300 100 L380 70"/>
                    <path d="M90 90 L120 40 L220 40"/>
                    <path d="M150 120 L180 160 L300 100"/>
                </g>
                <g fill="#EFA646">
                    <circle cx="20" cy="140" r="3"/><circle cx="90" cy="90" r="3"/><circle cx="150" cy="120" r="3"/>
                    <circle cx="220" cy="40" r="3.5"/><circle cx="300" cy="100" r="3"/><circle cx="380" cy="70" r="3"/>
                    <circle cx="120" cy="40" r="2.5"/><circle cx="180" cy="160" r="2.5"/>
                </g>
            </svg>
        </section>
        <section class="login-panel">
            <div class="login-status telemetry"><i class="dot"></i> Operasional normal</div>
            <form class="login-card" method="POST" action="{{ route('login') }}">
                @csrf
                <p class="quiet">Autentikasi internal</p>
                <h1>Login</h1>
                <p class="quiet">Gunakan akun Staff atau Supervisor.</p>

                <label for="email">Email</label>
                <input id="email" data-login-email type="email" name="email" value="{{ old('email') }}" autocomplete="username" required>
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label for="password">Password</label>
                <div class="password-wrap">
                    <input id="password" data-login-password type="password" name="password" autocomplete="current-password" required>
                    <button type="button" data-toggle-password aria-pressed="false">Show</button>
                </div>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <label class="check">
                    <input type="checkbox" name="remember" value="1">
                    Ingat sesi di perangkat ini
                </label>

                <button class="btn btn-gold btn-block" type="submit">Login</button>

                <div class="demo">
                    <header>
                        <span>AKSES DEMO</span>
                        <span>LINGKUNGAN UJI</span>
                    </header>
                    <div class="demo-line">
                        <button class="row" type="button" data-demo data-email="supervisor@mail.com" data-password="supervisor">
                            <strong>Supervisor</strong>
                            <span class="mono">supervisor@mail.com</span>
                            <small>Password: supervisor</small>
                        </button>
                        <button class="copy" type="button" data-copy="supervisor">Copy</button>
                    </div>
                    <div class="demo-line">
                        <button class="row" type="button" data-demo data-email="staff@mail.com" data-password="staff123">
                            <strong>Staff</strong>
                            <span class="mono">staff@mail.com</span>
                            <small>Password: staff123</small>
                        </button>
                        <button class="copy" type="button" data-copy="staff123">Copy</button>
                    </div>
                </div>
                <p class="fine">Skyreach · Manajemen kontrak internal</p>
            </form>
        </section>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
