<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['rol', 'roles'])->orderBy('nombre')->orderBy('apellido')->get();
        $roles = Rol::orderBy('nombre')->get();

        return view('users.roles.index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'rol_id' => ['required', 'exists:roles,id'],
            'roles_secundarios' => ['nullable', 'array'],
            'roles_secundarios.*' => ['exists:roles,id'],
        ], [], [
            'rol_id' => 'rol principal',
            'roles_secundarios' => 'roles adicionales',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('users.roles.index')
                ->withErrors($validator)
                ->withInput($request->all() + ['_focused_user_id' => $user->id]);
        }

        $data = $validator->validated();

        $user->update([
            'rol_id' => $data['rol_id'],
        ]);

        $rolesAsignados = collect($data['roles_secundarios'] ?? [])
            ->push($data['rol_id'])
            ->unique()
            ->values();

        $user->roles()->sync($rolesAsignados);

        return redirect()
            ->route('users.roles.index')
            ->with('success', 'Se actualizaron los roles de ' . ($user->nombre_completo ?: 'el usuario') . '.');
    }
}
