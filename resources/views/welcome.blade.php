<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Sistema de Inclusión Educativa') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg?v=2') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico?v=2') }}">

        <!-- Fuentes -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --primary: #e53945;
                --primary-100: #fde6e7;
                --surface: #ffffff;
                --text: #1f2933;
                --muted: #4b5563;
                --border: #e6e7ed;
            }

            [data-theme="dark"] {
                --primary: #e53945;
                --primary-100: #3a1f22;
                --surface: #1a1a1a;
                --text: #f5f5f5;
                --muted: #a0a0a0;
                --border: #2d2d2d;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                color: var(--text);
                background: radial-gradient(circle at 20% 20%, #ffffffff, #ffffff 35%),
                    radial-gradient(circle at 80% 10%, #f8f0ff, #ffffff 30%),
                    linear-gradient(180deg, #fff7f7 0%, #f8fafc 100%);
                transition: background 0.3s ease, color 0.3s ease;
            }

            [data-theme="dark"] body {
                background: radial-gradient(circle at 20% 20%, #1a1a1a, #1a1a1a 35%),
                    radial-gradient(circle at 80% 10%, #1a0f1f, #1a1a1a 30%),
                    linear-gradient(180deg, #1a0f0f 0%, #0f0f14 100%);
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            .landing-body {
                min-height: 100vh;
                display: flex;
                justify-content: center;
                padding: 32px 18px 48px;
            }

            .page-shell {
                width: min(1180px, 100%);
                display: flex;
                flex-direction: column;
                gap: 32px;
            }

            .top-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-shrink: 0;
            }

            .logo-slot {
                padding: 4px;
                width: auto;
                min-height: 90px;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: visible;
                padding: 0;
                position: relative;
            }

            /* Estilos para el logo en modo claro */
            .logo-slot #logo-light {
                max-width: 260px;
                height: 100px;
                width: auto;
                object-fit: contain;
                transition: opacity 0.3s ease;
                display: block;
            }

            /* Estilos para el logo en modo oscuro */
            .logo-slot #logo-dark {
                max-width: 100px;
                height: 95px;
                width: auto;
                object-fit: contain;
                transition: opacity 0.3s ease;
                display: block;
            }

            .brand-text {
                display: flex;
                flex-direction: column;
                line-height: 1.1;
            }

            .brand-name {
                font-weight: 700;
                font-size: 18px;
                color: var(--primary);
            }

            .brand-subtitle {
                font-size: 12px;
                color: var(--muted);
            }

            .nav-actions {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                border-radius: 10px;
                padding: 10px 14px;
                font-weight: 600;
                border: 1px solid transparent;
                transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.2s ease, border 0.2s ease;
                cursor: pointer;
                white-space: nowrap;
            }

            .btn.primary {
                background: var(--primary);
                color: #ffffff;
                box-shadow: 0 16px 40px rgba(229, 57, 69, 0.18);
            }

            .btn.primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 18px 44px rgba(229, 57, 69, 0.26);
            }

            .btn.secondary {
                background: #ffffff;
                border: 1px solid var(--border);
                color: var(--text);
            }

            .btn.secondary:hover,
            .btn.ghost:hover {
                transform: translateY(-1px);
                box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            }

            .btn.ghost {
                background: transparent;
                border: 1px solid var(--border);
                color: var(--text);
            }

            .btn.theme-toggle {
                background: transparent;
                border: 1px solid var(--border);
                color: var(--text);
                padding: 10px 12px;
                font-size: 18px;
            }

            .btn.theme-toggle:hover {
                transform: translateY(-1px);
                box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            }

            [data-theme="dark"] .btn.theme-toggle {
                background: rgba(255, 255, 255, 0.05);
            }

            .hero {
                display: grid;
                grid-template-columns: 1fr;
                gap: 24px;
                align-items: center;
                background: var(--surface);
                padding: 32px;
                border-radius: 24px;
                border: 1px solid var(--border);
                box-shadow: 0 26px 80px rgba(0, 0, 0, 0.06);
                transition: background 0.3s ease, border-color 0.3s ease;
            }

            [data-theme="dark"] .hero {
                box-shadow: 0 26px 80px rgba(0, 0, 0, 0.3);
            }

            .hero-copy h1 {
                font-size: clamp(32px, 5vw, 46px);
                margin: 10px 0 12px;
                font-weight: 700;
            }

            .highlight {
                color: var(--primary);
            }

            .lead {
                color: var(--muted);
                font-size: 17px;
                line-height: 1.6;
                margin-bottom: 20px;
            }

            .cta-group {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .pill {
                margin-top: 18px;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: var(--primary-100);
                color: #9f1239;
                padding: 10px 12px;
                border-radius: 999px;
                font-weight: 600;
                border: 1px solid #f7c2c6;
                transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }

            [data-theme="dark"] .pill {
                color: #ff6b7a;
                border-color: #4a2f32;
            }

            .pill .dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--primary);
                box-shadow: 0 0 0 6px rgba(229, 57, 69, 0.12);
            }

            .eyebrow {
                letter-spacing: 0.08em;
                text-transform: uppercase;
                font-size: 12px;
                font-weight: 700;
                color: #9f1239;
            }

            .features {
                background: var(--surface);
                padding: 28px;
                border-radius: 20px;
                border: 1px solid var(--border);
                box-shadow: 0 18px 50px rgba(0, 0, 0, 0.04);
                transition: background 0.3s ease, border-color 0.3s ease;
            }

            [data-theme="dark"] .features {
                box-shadow: 0 18px 50px rgba(0, 0, 0, 0.3);
            }

            .section-head {
                text-align: center;
                max-width: 720px;
                margin: 0 auto 22px;
            }

            .section-head h2 {
                margin: 8px 0 6px;
                font-size: 28px;
                font-weight: 700;
            }

            .muted {
                color: var(--muted);
            }

            .features-grid {
                margin-top: 18px;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }

            .feature-card {
                padding: 18px;
                border-radius: 14px;
                border: 1px solid var(--border);
                background: linear-gradient(180deg, #fffefe 0%, #ffffff 100%);
                box-shadow: 0 14px 36px rgba(0, 0, 0, 0.03);
                min-height: 168px;
                display: flex;
                flex-direction: column;
                gap: 8px;
                transition: background 0.3s ease, border-color 0.3s ease;
            }

            [data-theme="dark"] .feature-card {
                background: linear-gradient(180deg, #1f1f1f 0%, #1a1a1a 100%);
                box-shadow: 0 14px 36px rgba(0, 0, 0, 0.2);
            }

            .icon-circle {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                background: #ffeeef;
                display: grid;
                place-items: center;
                font-size: 20px;
                transition: background 0.3s ease;
            }

            [data-theme="dark"] .icon-circle {
                background: #3a1f22;
            }

            .footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 8px 4px 0;
                color: var(--muted);
                font-size: 14px;
            }

            .footer-brand {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .footer-icon {
                width: 32px;
                height: 32px;
                border-radius: 10px;
                background: #ffeeef;
                display: grid;
                place-items: center;
                font-size: 16px;
                transition: background 0.3s ease;
            }

            [data-theme="dark"] .footer-icon {
                background: #3a1f22;
            }

            @media (max-width: 980px) {
                .top-bar {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .nav-actions {
                    width: 100%;
                    justify-content: flex-end;
                }
            }

            @media (max-width: 640px) {
                .landing-body {
                    padding: 18px 14px 32px;
                }

                .btn {
                    width: 100%;
                }

                .cta-group {
                    flex-direction: column;
                    align-items: stretch;
                }

                .nav-actions {
                    flex-wrap: wrap;
                    gap: 8px;
                }
            }
        </style>
    </head>
    <body class="landing-body">
        <div class="page-shell">
            <header class="top-bar">
                <div class="brand">
                    <div class="logo-slot">
                        <img id="logo-light" src="{{ asset('images/inacap-logo-png_seeklogo-181445.png') }}" alt="Logo INACAP" loading="lazy" data-theme="light" />
                        <img id="logo-dark" src="{{ asset('images/logo-inacap-blanco.png') }}" alt="Logo INACAP" loading="lazy" data-theme="dark" style="display: none;" />
                    </div>
                    <div class="brand-text">
                        <span class="brand-name">SIP</span>
                        <span class="brand-subtitle">Sistema de Integración Pedagógica</span>
                    </div>
                </div>

                @if (Route::has('login'))
                    <nav class="nav-actions">
                        <button id="theme-toggle" class="btn theme-toggle" aria-label="Cambiar tema">
                            <span id="theme-icon">🌙</span>
                        </button>
                        @auth
                            <a href="{{ url('/home') }}" class="btn ghost">Panel</a>
                        @else
                            <a href="{{ route('login') }}" class="btn ghost">Iniciar sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn ghost">Registrarse</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <main class="hero" aria-labelledby="hero-title">
                <div class="hero-copy">
                    <p class="eyebrow">Plataforma integral</p>
                  <h1 id="hero-title">Sistema de <span class="highlight">Inclusión</span> Educativa</h1>
                    <p class="lead">
                        Plataforma para la gestión de casos de inclusión, seguimiento de estudiantes con necesidades
                        educativas especiales y coordinación entre equipos multidisciplinarios.
                    </p>
                    <div class="cta-group">
                        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="btn primary">Acceder al sistema</a>
                    </div>
                    <div class="pill">
                        <span class="dot"></span>
                        Seguimiento de estudiantes • Gestión de casos • Trabajo colaborativo
                    </div>
                </div>
            </main>

            <section id="caracteristicas" class="features" aria-label="Características del sistema">
                <header class="section-head">
                    <p class="eyebrow">Características principales</p>
                    <h2>Herramientas diseñadas para la inclusión educativa</h2>
                    <p class="muted">Simplifica la gestión institucional y fortalece el trabajo interdisciplinario.</p>
                </header>

                <div class="features-grid">
                    <article class="feature-card">
                        <div class="icon-circle">👥</div>
                        <h3>Gestión integral</h3>
                        <p>Administra estudiantes, casos y ajustes razonables desde una plataforma centralizada.</p>
                    </article>

                    <article class="feature-card">
                          <div class="icon-circle">🔒</div>
                        <h3>Seguridad y privacidad</h3>
                        <p>Sistema seguro que protege la información sensible y los historiales de cada estudiante.</p>
                    </article>

                    <article class="feature-card">
                        <div class="icon-circle">📊</div>
                        <h3>Reportes detallados</h3>
                        <p>Genera reportes claros para seguimiento, auditoría institucional y toma de decisiones.</p>
                    </article>

                    <article class="feature-card">
                        <div class="icon-circle">🤝</div>
                        <h3>Trabajo colaborativo</h3>
                        <p>Facilita la coordinación entre docentes, directivos y equipos de apoyo especializado.</p>
                    </article>
                </div>
            </section>

            <footer class="footer">
                <div class="footer-brand">
                    <span class="footer-icon">🎓</span>
                    <span class="brand-subtitle">Sistema de Inclusión Educativa</span>
                </div>
                <span class="muted">2025 -- Diseñado por Leonardo G. Vicente R. y Marcelo Z.</span>
            </footer>
        </div>

        <script>
            // Funcionalidad del modo oscuro
            (function() {
                const themeToggle = document.getElementById('theme-toggle');
                const themeIcon = document.getElementById('theme-icon');
                const logoLight = document.getElementById('logo-light');
                const logoDark = document.getElementById('logo-dark');
                const html = document.documentElement;

                // Cargar tema guardado o usar preferencia del sistema
                function loadTheme() {
                    const savedTheme = localStorage.getItem('theme');
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    const theme = savedTheme || (prefersDark ? 'dark' : 'light');
                    
                    html.setAttribute('data-theme', theme);
                    updateIcon(theme);
                    updateLogo(theme);
                }

                // Actualizar icono según el tema
                function updateIcon(theme) {
                    themeIcon.textContent = theme === 'dark' ? '☀️' : '🌙';
                }

                // Actualizar logo según el tema
                function updateLogo(theme) {
                    if (theme === 'dark') {
                        logoLight.style.display = 'none';
                        logoDark.style.display = 'block';
                    } else {
                        logoLight.style.display = 'block';
                        logoDark.style.display = 'none';
                    }
                }

                // Toggle del tema
                themeToggle.addEventListener('click', function() {
                    const currentTheme = html.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    html.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateIcon(newTheme);
                    updateLogo(newTheme);
                });

                // Cargar tema al iniciar
                loadTheme();

                // Escuchar cambios en la preferencia del sistema
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    if (!localStorage.getItem('theme')) {
                        const theme = e.matches ? 'dark' : 'light';
                        html.setAttribute('data-theme', theme);
                        updateIcon(theme);
                        updateLogo(theme);
                    }
                });
            })();
        </script>
    </body>
</html>
