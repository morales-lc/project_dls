<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentFaculty;

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

        $sf = StudentFaculty::where('username', $request->username)->first();
        if ($sf && $sf->user) {
            $user = $sf->user;
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->intended('/');
            }
        } else {
            // Try to login as admin/librarian using username or email
            $user = \App\Models\User::where('email', $request->username)
                ->orWhere('username', $request->username)
                ->first();
            if ($user) {
                // If admin/librarian
                if (in_array($user->role, ['admin', 'librarian']) && Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                    $request->session()->regenerate();
                    if ($user->role === 'admin') {
                        return redirect()->intended(route('admin.dashboard'));
                    }
                    return redirect()->intended('/');
                }
                // If student/faculty (created via Google login, no username)
                if (in_array($user->role, ['student', 'faculty']) && Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                    $request->session()->regenerate();
                    return redirect()->intended('/');
                }
            }
        }
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
