<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileCompleted
{
    public function handle(Request $request, Closure $next)
    {
        // If not authenticated, let other middleware handle
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip for admin/librarian/guest
        if (in_array($user->role, ['admin', 'librarian', 'guest'])) {
            return $next($request);
        }

        // Allowlist some routes/paths
        $routeName = optional($request->route())->getName();
        $path = trim($request->path(), '/');
        $allowedNames = [
            'profile.complete',
            'api.programs',
            'api.programs.courses',
        ];
        $allowedPaths = [
            'profile/complete',          // POST (unnamed)
            'auth/google',               // Google redirect
            'auth/google/callback',
            'logout',
            'login',
        ];
        if (in_array($routeName, $allowedNames, true) || in_array($path, $allowedPaths, true)) {
            return $next($request);
        }

        // Determine profile completion for student/faculty
        $sf = $user->studentFaculty;
        $isComplete = false;
        if ($sf) {
            // must have birthdate and school_id
            $hasCore = !empty($sf->birthdate) && !empty($sf->school_id);
            if ($sf->role === 'student') {
                $isComplete = $hasCore && !empty($sf->program_id) && !empty($sf->yrlvl);
            } else {
                // faculty: relax requirements to core only
                $isComplete = $hasCore;
            }
        }

        if (!$isComplete) {
            return redirect()->route('profile.complete')->with('error', 'Please complete your profile to continue.');
        }

        return $next($request);
    }
}
