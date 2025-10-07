<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function completeProfile(Request $request)
    {
        $request->validate([
            'school_id' => 'required|regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|string|min:6',
            'course' => 'nullable|string|max:255',
            'yrlvl' => 'nullable|string|max:10',
            'department' => 'nullable|string|max:255',
            'birthdate' => 'required|date',
            'role' => 'required|in:student,faculty',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(Auth::id());

        // Update user table
        $user->name = $request->username;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // Handle profile picture upload or Google profile picture
        $profilePic = $user->studentFaculty->profile_picture;
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $profilePic = $file->hashName();
            $file->storeAs('profile_pictures', $profilePic, 'public');
        } elseif (filter_var($profilePic, FILTER_VALIDATE_URL)) {
            // If profile picture is a Google URL, keep it
            // No action needed
        }

        // Update student_faculty table
        $user->studentFaculty->update([
            'school_id' => $request->school_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => $request->filled('password') ? bcrypt($request->password) : $user->studentFaculty->password,
            'course' => $request->course,
            'yrlvl' => $request->yrlvl,
            'department' => $request->department,
            'birthdate' => $request->birthdate,
            'role' => $request->role,
            'profile_picture' => $profilePic,
        ]);

        $updated = $user->studentFaculty->fresh();

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Profile updated', 'data' => $updated]);
        }

        return redirect('/')->with('success', 'Profile completed successfully!');
    }
}
