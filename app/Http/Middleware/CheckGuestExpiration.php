<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckGuestExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is a guest and if their account has expired
        if ($user && $user->role === 'guest') {
            if ($user->guest_account_status === 'expired' || 
                ($user->guest_expires_at && Carbon::parse($user->guest_expires_at)->isPast())) {
                
                // Mark as expired if not already
                if ($user->guest_account_status !== 'expired') {
                    $user->guest_account_status = 'expired';
                    $user->save();
                }
                
                // Log out the user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->with('error', 'Your guest account has expired. Please submit a new ALINET request to get temporary access.');
            }
        }

        return $next($request);
    }
}
