@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@push('styles')
<style>
    :root {
        --sie-primary: #c1121f;
        --sie-primary-dark: #8d0d17;
        --sie-background: #f6f7fb;
        --sie-text: #1f1f1f;
        --sie-muted: #6b7280;
        --sie-border: #e5e7eb;
    }

    body.guest-body {
        min-height: 100vh;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background: radial-gradient(circle at top left, rgba(193, 18, 31, 0.12), transparent 55%),
            radial-gradient(circle at bottom right, rgba(193, 18, 31, 0.12), transparent 45%),
            var(--sie-background);
        color: var(--sie-text);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .login-shell {
        width: min(960px, 100%);
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
        align-items: center;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
        padding: clamp(2rem, 4vw, 3rem);
        position: relative;
        overflow: hidden;
    }

    .login-shell::before {
        content: '';
        position: absolute;
        inset: auto -10% -45% -10%;
        background: linear-gradient(135deg, rgba(193, 18, 31, 0.12), transparent 60%);
        height: 60%;
        z-index: 0;
    }

    .login-brand {
        position: absolute;
        top: 1.25rem;
        left: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        color: var(--sie-primary);
    }

    .login-brand i {
        font-size: 1.4rem;
    }

    .login-shell .intro {
        z-index: 1;
    }

    .login-shell .intro h1 {
        font-size: clamp(2rem, 2.6vw, 2.75rem);
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: var(--sie-text);
    }

    .login-shell .intro p {
        margin: 0;
        color: var(--sie-muted);
        line-height: 1.6;
    }

    .login-shell .intro .highlight {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        padding: 0.55rem 1rem;
        background: rgba(193, 18, 31, 0.08);
        color: var(--sie-primary);
        border-radius: 999px;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .login-card {
        z-index: 1;
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        padding: clamp(1.75rem, 4vw, 2.4rem);
    }

    .login-card .card-title {
        margin-bottom: 0.35rem;
        font-weight: 600;
        font-size: 1.85rem;
        color: var(--sie-text);
    }

    .login-card .card-subtitle {
        font-size: 0.97rem;
        color: var(--sie-muted);
        margin-bottom: 1.75rem;
    }

    .back-link {
        position: absolute;
        top: 1.5rem;
        right: 1.75rem;
        color: var(--sie-muted);
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: var(--sie-primary);
    }

    .form-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--sie-text);
    }

    .form-control {
        border-radius: 12px;
        border: 1px solid var(--sie-border);
        padding: 0.75rem 1rem;
        font-size: 0.98rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--sie-primary);
        box-shadow: 0 0 0 0.2rem rgba(193, 18, 31, 0.18);
    }

    .form-check-label {
        color: var(--sie-muted);
    }

    .btn-login {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--sie-primary), var(--sie-primary-dark));
        border: none;
        padding: 0.85rem 1.4rem;
        font-weight: 600;
        font-size: 1rem;
        color: #ffffff;
        width: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-login:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 30px rgba(193, 18, 31, 0.22);
        color: #ffffff;
    }

    .forgot-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 500;
        color: var(--sie-primary);
        text-decoration: none;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    .admin-note {
        display: inline-block;
        margin-top: 0.75rem;
        font-size: 0.85rem;
        color: var(--sie-muted);
    }

    @media (max-width: 768px) {
        body.guest-body {
            padding: 1.5rem 1rem;
        }

        .login-shell {
            border-radius: 18px;
            padding: 2rem 1.5rem;
        }

        .login-brand,
        .back-link {
            position: static;
            margin-bottom: 1rem;
        }

        .login-shell::before {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="login-shell">
    <div class="login-brand">
        <i class="fas fa-graduation-cap"></i>
        <span>Inacap sistema de Ayuda</span>
    </div>

    <a href="{{ url('/') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Volver
    </a>

    <div class="intro">
        <h1>Iniciar Sesión</h1>
        <p>Accede con tus credenciales institucionales para gestionar tus solicitudes y ajustes razonables dentro de la plataforma.</p>
        <div class="highlight">
            <i class="fas fa-shield-heart"></i>
            Acceso seguro y respaldado por el equipo de apoyo inclusivo
        </div>
    </div>

    <div class="login-card">
        <div class="card-title">Ingrese sus credenciales</div>
        <div class="card-subtitle">Utiliza tu correo institucional para ingresar al sistema.</div>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico Institucional</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Recordar sesión</label>
                </div>

                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        <i class="fas fa-unlock-keyhole"></i>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-right-to-bracket"></i>
                Iniciar Sesión
            </button>

            <span class="admin-note">Acceso Admin habilitado mediante credenciales autorizadas.</span>
        </form>
    </div>
</div>
@endsection
