<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email'; // Mantener 'email' para compatibilidad con Laravel
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $emailOrRut = $request->get('email');
        $password = $request->get('password');

        if (!$emailOrRut) {
            return ['email' => null, 'password' => $password];
        }

        // Determinar si es email o RUT
        $isEmail = filter_var($emailOrRut, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Si es email, usar directamente
            return ['email' => $emailOrRut, 'password' => $password];
        }

        // Si no es email, asumir que es RUT
        $user = $this->findUserByRut($emailOrRut);
        
        if (!$user) {
            // Si no se encuentra el usuario, devolver credenciales inválidas
            return ['email' => null, 'password' => $password];
        }

        // Devolver el email del usuario encontrado por RUT
        return ['email' => $user->email, 'password' => $password];
    }

    /**
     * Buscar usuario por RUT a través de Estudiante o Docente
     *
     * @param  string  $rut
     * @return \App\Models\User|null
     */
    protected function findUserByRut(string $rut)
    {
        // Buscar en estudiantes
        $estudiante = \App\Models\Estudiante::where('rut', $rut)->first();
        if ($estudiante && $estudiante->user_id) {
            return \App\Models\User::find($estudiante->user_id);
        }

        // Buscar en docentes
        $docente = \App\Models\Docente::where('rut', $rut)->first();
        if ($docente && $docente->user_id) {
            return \App\Models\User::find($docente->user_id);
        }

        return null;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        // Validación flexible que acepta email o RUT
        $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El campo correo o RUT es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);
    }
    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->loadMissing('rol', 'roles');

        $hasNoRoles = is_null($user->rol) && $user->roles->isEmpty();
        if ($hasNoRoles && ! $user->superuser) {
            $user->forceFill(['superuser' => true])->save();
        }

        if ($user->superuser || $hasNoRoles) {
            return redirect()->route('home');
        }

        $roleNames = collect([
            optional($user->rol)->nombre,
        ])
            ->merge($user->roles->pluck('nombre'))
            ->filter()
            ->map(fn ($name) => mb_strtolower($name));

        if ($roleNames->contains('estudiante')) {
            return redirect()->route('estudiantes.dashboard');
        }

        if ($roleNames->contains('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($roleNames->contains('coordinadora de inclusion')) {
            return redirect()->route('coordinadora.dashboard');
        }

        if ($roleNames->contains('asesora pedagogica')) {
            return redirect()->route('asesora-pedagogica.dashboard');
        }

        if ($roleNames->contains('asesora tecnica pedagogica')) {
            return redirect()->route('asesora-tecnica.dashboard');
        }

        if ($roleNames->contains('docente')) {
            return redirect()->route('docente.dashboard');
        }

        if ($roleNames->contains('director de carrera')) {
            return redirect()->route('director.dashboard');
        }

        return redirect()->intended($this->redirectPath());
    }
}
