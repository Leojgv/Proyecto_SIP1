<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar Contraseña - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
</head>
<body class="auth-page" style="--auth-bg: url('{{ asset('images/auth/background.jpg') }}');">
    <div class="login-container">
        <div class="login-form-wrapper">
            <div class="login-form">
                <h1 class="text-uppercase">¿Olvidaste tu contraseña?</h1>

                <form id="forgotPasswordForm" method="POST" action="#" onsubmit="return false;">
                    @csrf

                    <div class="input-group">
                        <label for="email">Correo electrónico</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="Ingresa tu correo electrónico"
                               style="width: 100%;">
                        <i class="fas fa-envelope input-icon"></i>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <label for="telefono">Número de teléfono</label>
                        <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror"
                               name="telefono" value="{{ old('telefono') }}" required autocomplete="tel"
                               placeholder="Ingresa tu número de teléfono"
                               style="width: 100%;">
                        <i class="fas fa-phone input-icon"></i>
                        @error('telefono')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Aviso cuando coincidan los datos -->
                    <div id="codigoAviso" class="alert alert-info" style="display: none; margin-bottom: 1rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¡Datos verificados!</strong> El código de recuperación será enviado al presionar el Boton.
                    </div>

                    <div class="form-group mb-0">
                        <button type="submit" class="login-button" id="submitBtn">
                            Enviar código de recuperación
                        </button>
                    </div>

                    <div class="register-link">
                        <a class="btn btn-link" href="{{ route('login') }}">
                            <i class="fas fa-arrow-left me-1"></i>Volver a Iniciar Sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const telefonoInput = document.getElementById('telefono');
            const codigoAviso = document.getElementById('codigoAviso');
            const form = document.getElementById('forgotPasswordForm');
            const submitBtn = document.getElementById('submitBtn');

            // Función para normalizar teléfono (remover espacios, guiones, paréntesis)
            function normalizarTelefono(telefono) {
                return telefono.replace(/[\s\-\(\)]/g, '').trim();
            }

            // Lista de datos demostrativos (solo para demostración)
            const datosDemostrativos = [
                { email: 'estudiante@ejemplo.com', telefono: '+56912345678' },
                { email: 'user@test.com', telefono: '+56987654321' },
                { email: 'test@sistema.cl', telefono: '+56911223344' },
                { email: 'leonardog@sistema.cl', telefono: '+56934817230' },
                { email: 'leonardoG@sistema.cl', telefono: '+56934817230' },
            ];

            function verificarDatos() {
                const email = emailInput.value.trim();
                const telefono = telefonoInput.value.trim();

                if (email && telefono) {
                    const emailNormalizado = email.toLowerCase();
                    const telefonoNormalizado = normalizarTelefono(telefono);

                    // Buscar si coinciden los datos (demostrativo)
                    const coincide = datosDemostrativos.some(dato => {
                        const datoEmailNormalizado = dato.email.toLowerCase();
                        const datoTelefonoNormalizado = normalizarTelefono(dato.telefono);
                        return datoEmailNormalizado === emailNormalizado && 
                               datoTelefonoNormalizado === telefonoNormalizado;
                    });

                    if (coincide) {
                        codigoAviso.style.display = 'block';
                        codigoAviso.classList.remove('alert-danger');
                        codigoAviso.classList.add('alert-info');
                    } else {
                        codigoAviso.style.display = 'none';
                    }
                } else {
                    codigoAviso.style.display = 'none';
                }
            }

            // Escuchar cambios en los campos
            emailInput.addEventListener('input', verificarDatos);
            emailInput.addEventListener('blur', verificarDatos);
            telefonoInput.addEventListener('input', verificarDatos);
            telefonoInput.addEventListener('blur', verificarDatos);

            // Manejar el envío del formulario (demostrativo)
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = emailInput.value.trim();
                const telefono = telefonoInput.value.trim();

                if (!email || !telefono) {
                    alert('Por favor, completa todos los campos.');
                    return;
                }

                const emailNormalizado = email.toLowerCase();
                const telefonoNormalizado = normalizarTelefono(telefono);

                const coincide = datosDemostrativos.some(dato => {
                    const datoEmailNormalizado = dato.email.toLowerCase();
                    const datoTelefonoNormalizado = normalizarTelefono(dato.telefono);
                    return datoEmailNormalizado === emailNormalizado && 
                           datoTelefonoNormalizado === telefonoNormalizado;
                });

                if (coincide) {
                    alert('¡Código enviado!. El código se enviaría al teléfono: ' + telefono + ')');
                } else {
                    alert('Los datos ingresados no coinciden. Por favor, verifica tu correo y número de teléfono.');
                }
            });
        });
    </script>
</body>
</html>
