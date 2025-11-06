<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Registro - {{ config('app.name', 'Laravel') }}</title>

    <!-- Carga de CSS (Método tradicional sin Vite) -->
    <link href="{{ asset('css/custom-login.css') }}" rel="stylesheet">
    
    <!-- Font Awesome para los íconos (tu proyecto ya lo incluye vía adminlte) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
</head>
<body>
    <div class="login-container">
        <!-- Lado del Formulario -->
        <div class="login-form-wrapper">
            <div class="login-form">
                <h1>CREAR CUENTA</h1>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Campo Nombre -->
                    <div class="input-group">
                        <label for="name">NOMBRE COMPLETO</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        <i class="fas fa-user input-icon"></i>
                        
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Campo Correo Electrónico -->
                    <div class="input-group">
                        <label for="email">CORREO ELECTRÓNICO</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                        <i class="fas fa-envelope input-icon"></i>
                        
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="input-group">
                        <label for="password">CONTRASEÑA</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        <i class="fas fa-lock input-icon"></i>
                        
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Campo Confirmar Contraseña -->
                    <div class="input-group">
                        <label for="password-confirm">CONFIRMAR CONTRASEÑA</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        <i class="fas fa-lock input-icon"></i>
                    </div>

                    <!-- Botón Registrarse -->
                    <div class="form-group mb-0" style="margin-bottom: 1rem !important;">
                        <button type="submit" class="login-button">
                            REGISTRARSE
                        </button>
                    </div>

                    <!-- Enlace para volver a Login -->
                    <div class="register-link">
                         <a class="btn btn-link" href="{{ route('login') }}">
                            ¿Ya tienes cuenta? Iniciar Sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Lado de la Imagen de Fondo -->
        <div class="login-background"></div>
    </div>
</body>
</html>