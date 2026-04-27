<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Mapping rôle -> role_id
        $roleMap = [
            'admin' => 1,
            'client' => 2,
            'employe' => 3,
        ];

        $allowedRoleIds = collect($roles)
            ->map(fn ($role) => $roleMap[$role] ?? null)
            ->filter()
            ->toArray();

        if (! in_array($user->role_id, $allowedRoleIds)) {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}
