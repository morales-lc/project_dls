<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Program;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

   public function completeProfile(Request $request)
{
    $userId = Auth::id();

    $rules = [
        'school_id' => ['required', 'regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/'],
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $userId],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
        'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        'course' => ['nullable', 'string', 'max:255'],
        'yrlvl' => ['nullable', 'string', 'max:10'],
        'program_id' => ['nullable', 'exists:programs,id'],
        'birthdate' => ['required', 'date'],
        'role' => ['required', 'in:student,faculty'],
        'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
    ];

    // If role is student, require these fields
    if ($request->input('role') === 'student') {
        $rules['program_id'][] = 'required';
        $rules['course'][] = 'required';
        $rules['yrlvl'][] = 'required'; // ✅ Added this line
    }

    $messages = [
        'school_id.required' => 'School ID is required and must match format (e.g. C22-0171).',
        'school_id.regex' => 'School ID format is invalid. Use CXX-XXXX where X are digits.',
        'first_name.required' => 'First name is required.',
        'last_name.required' => 'Last name is required.',
        'username.required' => 'Username is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Email must be a valid email address.',
        'email.unique' => 'The provided email is already in use.',
        'password.min' => 'Password must be at least 6 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'program_id.required' => 'Please select a program.',
        'course.required' => 'Please select a course.',
        'yrlvl.required' => 'Year level is required for students.', // ✅ Added message
        'birthdate.required' => 'Birthdate is required.',
    ];

    $validated = $request->validate($rules, $messages);

    $user = User::find(Auth::id());

    // Update user table
    $user->name = $request->first_name . ' ' . $request->last_name;
    $user->email = $request->email;
    $user->username = $request->username;
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    $user->save();

    // Handle profile picture upload
    $profilePic = $user->studentFaculty->profile_picture;
    if ($request->hasFile('profile_picture')) {
        $file = $request->file('profile_picture');
        $profilePic = $file->hashName();
        $file->storeAs('profile_pictures', $profilePic, 'public');
    }

    // Update student_faculty table
    $user->studentFaculty->update([
        'school_id' => $request->school_id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        // keep username in student_faculty only for display if you still need it later
        'username' => $request->username,
        'course' => $request->course,
        'yrlvl' => $request->yrlvl,
        'program_id' => $request->program_id ?? null,
        'birthdate' => $request->birthdate,
        'role' => $request->role,
        'profile_picture' => $profilePic,
    ]);

    $updated = $user->studentFaculty->fresh();

    if ($request->expectsJson()) {
        return response()->json([
            'status' => 'ok',
            'message' => 'Profile updated successfully!',
            'data' => $updated
        ]);
    }

    return redirect('/')->with('success', 'Profile updated successfully!');
}

}
