<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SIE | Acceso Administración</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@0.7.2/dist/tailwind-ui.min.css">
        @endif
    </head>
    <body class="antialiased bg-slate-950 text-slate-900">
        <div class="relative overflow-hidden">
            <header class="relative z-20 border-b border-slate-200/60 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 text-lg font-semibold text-slate-900">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-red-600 text-white shadow-lg">SIE</span>
                        <div class="leading-tight">
                            <span class="block text-sm text-slate-500">Sistema de</span>
                            <span class="block text-base font-semibold tracking-wide">Inclusión Educativa</span>
                        </div>
                    </a>
                    <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                        <a href="#servicios" class="transition hover:text-red-600">Características</a>
                        <a href="#perfiles" class="transition hover:text-red-600">Perfiles</a>
                        <a href="#acceso" class="transition hover:text-red-600">Acceso</a>
                    </nav>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="hidden rounded-full border border-red-600/30 px-4 py-2 text-sm font-semibold text-red-600 transition hover:border-red-600 hover:bg-red-50 md:inline-flex">Iniciar Sesión</a>
                        <a href="#acceso" class="inline-flex rounded-full bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-red-500/30 transition hover:from-red-600 hover:to-red-700">Acceder</a>
                    </div>
                </div>
            </header>

            <main>
                <section class="relative overflow-hidden bg-gradient-to-br from-red-600 via-red-500 to-red-700 py-20 text-white">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),_transparent_50%)]"></div>
                    <div class="relative mx-auto flex max-w-6xl flex-col gap-12 px-6 md:flex-row md:items-center">
                        <div class="md:w-7/12">
                            <div class="inline-flex items-center rounded-full border border-white/30 bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white/80">Plataforma integral para la inclusión</div>
                            <h1 class="mt-6 text-4xl font-semibold leading-tight md:text-5xl">
                                Gestión educativa con enfoque en <span class="text-white/90">inclusión</span> y acompañamiento especializado.
                            </h1>
                            <p class="mt-6 text-lg text-white/80">
                                Administra estudiantes, equipos de apoyo y procesos académicos en un entorno centralizado, seguro y diseñado para instituciones educativas comprometidas con la inclusión.
                            </p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#acceso" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-red-600 shadow-lg shadow-red-900/10 transition hover:bg-red-50">Acceso Administrador</a>
                                <a href="#servicios" class="inline-flex items-center justify-center rounded-full border border-white/50 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">Conocer más</a>
                            </div>
                            <dl class="mt-12 grid grid-cols-2 gap-6 text-sm text-white/80 md:grid-cols-4">
                                <div>
                                    <dt class="font-semibold text-white">Gestión de Casos</dt>
                                    <dd class="mt-1 text-white/70">Seguimiento especializado para cada estudiante.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Coordinación</dt>
                                    <dd class="mt-1 text-white/70">Sincroniza acciones entre docentes y asesores.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Reportes</dt>
                                    <dd class="mt-1 text-white/70">Indicadores en tiempo real para la toma de decisiones.</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-white">Seguridad</dt>
                                    <dd class="mt-1 text-white/70">Autenticación y perfiles por rol.</dd>
                                </div>
                            </dl>
                        </div>
                        <div class="md:w-5/12">
                            <div class="overflow-hidden rounded-3xl border border-white/20 bg-white/10 shadow-2xl shadow-red-900/30 backdrop-blur">
                                <div class="border-b border-white/10 bg-white/5 px-6 py-4">
                                    <p class="text-sm font-semibold text-white">Panel Administrativo</p>
                                    <p class="text-xs text-white/70">Control total sobre usuarios, casos y reportes.</p>
                                </div>
                                <div class="space-y-6 px-6 py-6 text-sm text-white/80">
                                    <div>
                                        <p class="text-xs uppercase tracking-widest text-white/60">Casos activos</p>
                                        <p class="text-3xl font-semibold text-white">245</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 text-xs">
                                        <div class="rounded-2xl bg-white/10 p-4">
                                            <p class="font-semibold text-white">Docentes vinculados</p>
                                            <p class="mt-2 text-2xl font-semibold">38</p>
                                            <p class="mt-1 text-white/60">Equipos interdisciplinarios en línea.</p>
                                        </div>
                                        <div class="rounded-2xl bg-white/10 p-4">
                                            <p class="font-semibold text-white">Estudiantes</p>
                                            <p class="mt-2 text-2xl font-semibold">127</p>
                                            <p class="mt-1 text-white/60">Con seguimiento activo y planes personalizados.</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-widest text-white/60">Notificaciones recientes</p>
                                        <ul class="mt-3 space-y-3">
                                            <li class="flex items-center gap-3">
                                                <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                                <span>Nuevo caso asignado a la carrera de Trabajo Social.</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                                                <span>Actualizar informe de acompañamiento pedagógico.</span>
                                            </li>
                                            <li class="flex items-center gap-3">
                                                <span class="h-2 w-2 rounded-full bg-sky-300"></span>
                                                <span>Recordatorio de entrevista pendiente para Ana González.</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="servicios" class="bg-white py-20">
                    <div class="mx-auto max-w-6xl px-6">
                        <div class="mx-auto max-w-3xl text-center">
                            <p class="text-sm font-semibold uppercase tracking-widest text-red-600">Gestión centrada en las personas</p>
                            <h2 class="mt-4 text-3xl font-semibold text-slate-900">Herramientas diseñadas para la inclusión educativa</h2>
                            <p class="mt-4 text-base text-slate-600">Consolida los procesos de apoyo, seguimiento y evaluación en un sistema que prioriza la accesibilidad y la colaboración entre equipos.</p>
                        </div>
                        <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-3xl border border-slate-200/80 p-6 shadow-sm transition hover:-translate-y-1 hover:border-red-500/60 hover:shadow-lg">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-slate-900">Gestión integral</h3>
                                <p class="mt-3 text-sm text-slate-600">Organiza casos, asigna responsables y coordina acciones de apoyo desde un solo lugar.</p>
                            </div>
                            <div class="rounded-3xl border border-slate-200/80 p-6 shadow-sm transition hover:-translate-y-1 hover:border-red-500/60 hover:shadow-lg">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6a4.5 4.5 0 10-9 0v4.5M4.5 10.5h15L18 21H6l-1.5-10.5z" />
                                    </svg>
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-slate-900">Privacidad garantizada</h3>
                                <p class="mt-3 text-sm text-slate-600">Perfiles de acceso personalizados y trazabilidad de actividades para cumplir estándares institucionales.</p>
                            </div>
                            <div class="rounded-3xl border border-slate-200/80 p-6 shadow-sm transition hover:-translate-y-1 hover:border-red-500/60 hover:shadow-lg">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5M3.75 12h16.5m-16.5 6.75h16.5" />
                                    </svg>
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-slate-900">Reportes detallados</h3>
                                <p class="mt-3 text-sm text-slate-600">Indicadores claves, métricas de avance y descarga de informes en pocos pasos.</p>
                            </div>
                            <div class="rounded-3xl border border-slate-200/80 p-6 shadow-sm transition hover:-translate-y-1 hover:border-red-500/60 hover:shadow-lg">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25l3.75 3.75L21 2.25m-10.5 7.5L3.75 16.5 7.5 21l9-9" />
                                    </svg>
                                </div>
                                <h3 class="mt-6 text-lg font-semibold text-slate-900">Apoyo educativo</h3>
                                <p class="mt-3 text-sm text-slate-600">Historial de intervenciones y seguimiento colaborativo entre equipos.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="perfiles" class="bg-slate-50 py-20">
                    <div class="mx-auto max-w-6xl px-6">
                        <div class="grid gap-10 md:grid-cols-[2fr,3fr] md:items-center">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-widest text-red-600">Perfiles de acceso</p>
                                <h2 class="mt-4 text-3xl font-semibold text-slate-900">Accesos diferenciados según rol institucional</h2>
                                <p class="mt-4 text-base text-slate-600">Cada perfil cuenta con un panel personalizado, accesos controlados y herramientas acordes a sus responsabilidades. Comenzamos con el panel de administración y pronto habilitaremos las vistas para docentes, asesores pedagógicos y estudiantes.</p>
                                <ul class="mt-6 space-y-3 text-sm text-slate-600">
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-100 text-xs font-semibold text-red-600">1</span>
                                        Control y configuración global del sistema.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600">2</span>
                                        Coordinación académica y seguimiento docente.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600">3</span>
                                        Acompañamiento pedagógico y seguimiento de casos.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-200 text-xs font-semibold text-slate-600">4</span>
                                        Autogestión y retroalimentación para estudiantes.
                                    </li>
                                </ul>
                            </div>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <article class="rounded-3xl border border-red-500/40 bg-white p-6 shadow-lg shadow-red-200">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-900">Administrador</h3>
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-red-600">Activo</span>
                                    </div>
                                    <p class="mt-4 text-sm text-slate-600">Monitorea toda la operación, gestiona usuarios y configura flujos de trabajo.</p>
                                    <ul class="mt-6 space-y-2 text-sm text-slate-600">
                                        <li class="flex items-center gap-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Panel con indicadores clave.
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Gestión de usuarios y permisos.
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Configuración institucional.
                                        </li>
                                    </ul>
                                </article>
                                <article class="rounded-3xl border border-slate-200 bg-white p-6 opacity-70">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-900">Docente</h3>
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-600">Próximamente</span>
                                    </div>
                                    <p class="mt-4 text-sm text-slate-600">Acceso a estudiantes, planificaciones y registros de apoyo pedagógico.</p>
                                </article>
                                <article class="rounded-3xl border border-slate-200 bg-white p-6 opacity-70">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-900">Asesor Pedagógico</h3>
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-600">Próximamente</span>
                                    </div>
                                    <p class="mt-4 text-sm text-slate-600">Herramientas para seguimiento y coordinación con docentes y familias.</p>
                                </article>
                                <article class="rounded-3xl border border-slate-200 bg-white p-6 opacity-70">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-slate-900">Estudiante</h3>
                                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-600">Próximamente</span>
                                    </div>
                                    <p class="mt-4 text-sm text-slate-600">Visualización de apoyos, tareas y comunicación con el equipo.</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="acceso" class="bg-white py-20">
                    <div class="mx-auto max-w-5xl px-6">
                        <div class="grid gap-12 lg:grid-cols-[1.2fr,1fr] lg:items-center">
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-widest text-red-600">Acceso Administrador</p>
                                <h2 class="mt-4 text-3xl font-semibold text-slate-900">Inicia sesión para gestionar el ecosistema institucional</h2>
                                <p class="mt-4 text-base text-slate-600">Ingresa con tus credenciales institucionales para acceder al panel de control del Sistema de Inclusión Educativa. Desde aquí podrás administrar usuarios, asignar casos y supervisar el progreso académico.</p>
                                <dl class="mt-8 grid gap-6 sm:grid-cols-2">
                                    <div class="rounded-3xl border border-slate-200/80 p-5">
                                        <dt class="text-sm font-semibold text-slate-900">Usuarios Administradores</dt>
                                        <dd class="mt-2 text-sm text-slate-600">Gestionan la configuración general y supervisan indicadores críticos.</dd>
                                    </div>
                                    <div class="rounded-3xl border border-slate-200/80 p-5">
                                        <dt class="text-sm font-semibold text-slate-900">Soporte dedicado</dt>
                                        <dd class="mt-2 text-sm text-slate-600">Equipo institucional disponible para resolver incidencias y solicitudes.</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <div class="rounded-3xl border border-slate-200/80 bg-white p-8 shadow-xl shadow-red-100">
                                    <h3 class="text-lg font-semibold text-slate-900">Inicio de Sesión</h3>
                                    <p class="mt-1 text-sm text-slate-600">Utiliza tu correo institucional y contraseña para acceder.</p>
                                    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-6">
                                        @csrf
                                        <div>
                                            <label for="email" class="block text-sm font-semibold text-slate-900">Correo electrónico</label>
                                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 @error('email') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                            @error('email')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label for="password" class="block text-sm font-semibold text-slate-900">Contraseña</label>
                                                @if (Route::has('password.request'))
                                                    <a class="text-sm font-semibold text-red-600 transition hover:text-red-700" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                                                @endif
                                            </div>
                                            <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm transition focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200 @error('password') border-red-500 focus:border-red-500 focus:ring-red-200 @enderror">
                                            @error('password')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                                <input class="rounded border-slate-300 text-red-600 shadow-sm focus:ring-red-500" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <span>Recordar sesión</span>
                                            </label>
                                            <a href="#" class="text-sm font-semibold text-slate-400" aria-disabled="true">Acceso delegados (próx.)</a>
                                        </div>
                                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-red-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-red-300 transition hover:from-red-600 hover:to-red-700">Ingresar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 bg-white py-8">
                <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 text-center text-sm text-slate-500 md:flex-row md:text-left">
                    <div>
                        <p class="font-semibold text-slate-700">Sistema de Inclusión Educativa</p>
                        <p class="text-slate-500">© {{ date('Y') }} - Desarrollado para instituciones inclusivas comprometidas con el acompañamiento integral.</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="#servicios" class="transition hover:text-red-600">Características</a>
                        <a href="#perfiles" class="transition hover:text-red-600">Perfiles</a>
                        <a href="mailto:soporte@sie.edu" class="transition hover:text-red-600">Soporte</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
