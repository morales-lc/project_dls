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
    // Determine if this is an in-place edit (AJAX/JSON) vs initial completion form
    $isEdit = $request->expectsJson() || $request->ajax();

    $rules = [
        'school_id' => ['required'],
        'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
        'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $userId],
        // Password is required on the complete profile page, but optional when editing
        'password' => [
            $isEdit ? 'nullable' : 'required',
            'string',
            'min:8',
            'confirmed',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
        ],
        'course' => ['nullable', 'string', 'max:255'],
        'yrlvl' => ['nullable', 'string'],
        'program_id' => ['nullable', 'exists:programs,id'],
        'birthdate' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
        'role' => ['required', 'in:student,faculty'],
        'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:4096'],
    ];

    // Backend role-based validation
    if (in_array($request->input('role'), ['student', 'faculty'])) {
        // Program is required for both students and faculty
        $rules['program_id'][] = 'required';
    }
    if ($request->input('role') === 'student') {
        // Year level is required for all students
        $rules['yrlvl'][] = 'required';
        
        // Course is only required if NOT Junior High School
        $programId = $request->input('program_id');
        if ($programId) {
            $program = Program::find($programId);
            if ($program && $program->name !== 'Junior High School') {
                $rules['course'][] = 'required';
            }
        } else {
            // If no program selected yet, keep course required (will fail program validation first)
            $rules['course'][] = 'required';
        }
    }

    $messages = [
        'school_id.required' => 'School ID is required.',
        'school_id.regex' => 'School ID format is invalid. Use CXX-XXXX where X are digits.',
        'first_name.required' => 'First name is required.',
        'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
        'last_name.required' => 'Last name is required.',
        'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
        'username.required' => 'Username is required.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).',
        'program_id.required' => 'Please select a program.',
        'course.required' => 'Please select a course.',
        'yrlvl.required' => 'Year level is required for students.', 
        'birthdate.required' => 'Birthdate is required.',
        'birthdate.before_or_equal' => 'Birthdate cannot be in the future.',
        'birthdate.after' => 'Birthdate must be after January 1, 1900.',
    ];

    $validated = $request->validate($rules, $messages);

    $user = User::find(Auth::id());
    // Ensure we have a student_faculty record to work with
    $sf = $user->studentFaculty; // relation may or may not be loaded
    if (!$sf) {
        $sf = $user->studentFaculty()->firstOrCreate([]);
    }

    // Update user table (email is not editable here)
    $user->name = $request->first_name . ' ' . $request->last_name;
    $user->username = $request->username;
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
    }
    $user->save();

    // Handle profile picture upload (preserve existing if none uploaded)
    $profilePic = $sf->profile_picture ?? null;
    if ($request->hasFile('profile_picture')) {
        $file = $request->file('profile_picture');
        $profilePic = $file->hashName();
        $file->storeAs('profile_pictures', $profilePic, 'public');
    }

    // Normalize fields based on role
    $role = $request->role;
    $courseVal = $role === 'student' ? $request->course : null;
    $yrLvlVal = $role === 'student' ? $request->yrlvl : null;

    // Update student_faculty table using the ensured model
    $sf->update([
        'school_id' => $request->school_id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        // keep username in student_faculty only for display if you still need it later
        'username' => $request->username,
        'course' => $courseVal,
        'yrlvl' => $yrLvlVal,
        'program_id' => $request->program_id ?? null,
        'birthdate' => $request->birthdate,
        'role' => $role,
        'profile_picture' => $profilePic,
    ]);

    $updated = $sf->fresh();

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
