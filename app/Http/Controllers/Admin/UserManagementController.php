<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');
        $rolId = $request->query('rol');
        $usuariosQuery = User::with('rol')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('apellido', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($rolId, function ($query) use ($rolId) {
                $query->where('rol_id', $rolId);
            })
            ->orderBy('nombre')
            ->orderBy('apellido');

        $usuarios = $usuariosQuery->paginate(8)->withQueryString();

        $roles = Rol::withCount('users')->orderBy('nombre')->get();

        $totalUsuarios = User::count();
        $usuariosActivos = User::whereNotNull('email_verified_at')->count();
        $superusuarios = User::where('superuser', true)->count();
        $administradores = User::whereHas('rol', function ($query) {
            $query->where('nombre', 'like', '%admin%');
        })->count();

        return view('admin.users.index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'stats' => [
                'total' => $totalUsuarios,
                'activos' => $usuariosActivos,
                'administradores' => $administradores,
                'superusuarios' => $superusuarios,
            ],
            'roleBreakdown' => $roles,
            'search' => $search,
            'rolId' => $rolId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'rol_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'rol_id' => $data['rol_id'],
        ]);

        $user->roles()->sync([$data['rol_id']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'El usuario fue creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'rol_id' => ['required', 'exists:roles,id'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors($validator, 'editUser')
                ->withInput()
                ->with('edit_user_id', $user->id);
        }

        $data = $validator->validated();

        $user->nombre = $data['nombre'];
        $user->apellido = $data['apellido'];
        $user->email = $data['email'];
        $user->rol_id = $data['rol_id'];
        $user->save();

        $user->roles()->sync([$data['rol_id']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'El usuario fue actualizado correctamente.');
    }
}
