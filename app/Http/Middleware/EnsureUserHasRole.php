<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request ensuring the authenticated user has at least one of the required roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->superuser) {
            return $next($request);
        }

        $normalizedRequired = collect($roles)
            ->flatMap(fn ($role) => explode(',', $role))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->map(fn ($role) => mb_strtolower($role))
            ->unique()
            ->values();

        if ($normalizedRequired->isEmpty()) {
            abort(Response::HTTP_FORBIDDEN, 'No hay roles configurados para esta ruta.');
        }

        $user->loadMissing('rol', 'roles');

        $userRoles = collect([$user->rol?->nombre])
            ->merge($user->roles->pluck('nombre') ?? [])
            ->filter()
            ->map(fn ($role) => mb_strtolower($role))
            ->unique();

        if ($normalizedRequired->intersect($userRoles)->isNotEmpty()) {
            return $next($request);
        }

        abort(Response::HTTP_FORBIDDEN, 'No tienes permisos para acceder a esta sección.');
    }
}
