<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:librarian,admin')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $userRole = Auth::user()->role ?? null;
        if (!$userRole) abort(403);

        // normalize roles
        $roles = array_map('strtolower', $roles);
        if (!in_array(strtolower($userRole), $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
