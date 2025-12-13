<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
</head>
<body class="auth-page" style="--auth-bg: url('{{ asset('images/auth/background.jpg') }}');">
    <div class="login-container">
        <div class="login-form-wrapper">
            <div class="login-form">
                <h1 class="login-title">INICIAR SESIÓN</h1>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group">
                        <label for="email">Correo electrónico o RUT</label>
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="username" autofocus
                               placeholder="Ingresa tu correo o RUT">
                        <i class="fas fa-user input-icon"></i>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password">
                        <i class="fas fa-lock input-icon"></i>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="login-options">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="login-button">
                            INGRESAR
                        </button>
                    </div>

                    <div class="register-link">
                         <a class="btn btn-link" href="{{ route('register') }}">
                            ¿No tienes una cuenta? Regístrate
                         </a>
                    </div>
                </form>

                <div class="back-to-intro">
                    <a href="{{ url('/') }}" class="back-to-intro-button">
                        <i class="fas fa-arrow-left me-2"></i>Volver al intro
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
