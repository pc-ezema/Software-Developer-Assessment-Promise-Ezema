<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            return response()->json(['success' => false, 'message' => 'Role or user not found'], 404);
        }

        if (!$user->role->permissions->contains(fn($perm) => strtolower($perm->name) === strtolower($permission))) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        return $next($request);
    }
}
