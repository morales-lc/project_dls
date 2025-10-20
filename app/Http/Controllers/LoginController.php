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

        $user = \App\Models\User::where('email', $request->username)
            ->orWhere('username', $request->username)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
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
