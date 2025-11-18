<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\StudentFaculty;
use App\Mail\LoginOtpMail;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $request->username)
            ->orWhere('username', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if guest account has expired
            if ($user->role === 'guest') {
                if ($user->guest_account_status === 'expired' || 
                    ($user->guest_expires_at && $user->guest_expires_at->isPast())) {
                    // Mark as expired if not already
                    if ($user->guest_account_status !== 'expired') {
                        $user->guest_account_status = 'expired';
                        $user->save();
                    }
                    return back()->withErrors([
                        'username' => 'Your guest account has expired. Please submit a new ALINET request to get temporary access.',
                    ]);
                }
                
                // Guest accounts bypass OTP - log them in directly
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended(route('guest.dashboard'));
            }
            
            // Generate 6-digit OTP for non-guest users
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Store OTP and expiration time (5 minutes)
            $user->login_otp = $otp;
            $user->login_otp_expires_at = now()->addMinutes(5);
            $user->save();
            
            // Send OTP email
            try {
                Mail::to($user->email)->send(new LoginOtpMail($otp, $user->name));
            } catch (\Exception $e) {
                return back()->withErrors([
                    'username' => 'Failed to send verification code. Please try again.',
                ]);
            }
            
            // Store user ID in session for OTP verification
            $request->session()->put('otp_user_id', $user->id);
            
            return redirect()->route('login.verify.otp')->with('success', 'A verification code has been sent to your email.');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login');
        }
        
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found.']);
        }

        // Check if OTP is valid and not expired
        if ($user->login_otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        if (!$user->login_otp_expires_at || $user->login_otp_expires_at->isPast()) {
            return back()->withErrors(['otp' => 'Verification code has expired. Please login again.']);
        }

        // Clear OTP
        $user->login_otp = null;
        $user->login_otp_expires_at = null;
        $user->save();

        // Clear session
        $request->session()->forget('otp_user_id');

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();

        // Optional role-based redirection
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }
        if ($user->role === 'librarian') {
            return redirect()->intended(route('librarian.dashboard'));
        }
        if ($user->role === 'guest') {
            return redirect()->intended(route('guest.dashboard'));
        }
        
        return redirect()->intended('/');
    }

    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired. Please login again.']);
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['otp' => 'User not found.']);
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP and expiration time
        $user->login_otp = $otp;
        $user->login_otp_expires_at = now()->addMinutes(5);
        $user->save();
        
        // Send OTP email
        try {
            Mail::to($user->email)->send(new LoginOtpMail($otp, $user->name));
            return back()->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Failed to send verification code. Please try again.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
