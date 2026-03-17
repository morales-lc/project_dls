<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFaculty;
use App\Models\User;
use App\Models\Program;
use Illuminate\Validation\Rule;

class StudentFacultyController extends Controller
{
    public function edit($id)
    {
        $sf = StudentFaculty::with('user', 'program')->findOrFail($id);
        $programs = \App\Models\Program::all();
        return view('users.edit-student-faculty', compact('sf', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $sf = StudentFaculty::findOrFail($id);
        $user = $sf->user;
        $returnUrl = $request->input('return_url');

        // Get program name to determine if course is required
        $program = Program::find($request->program_id);
        $programName = $program ? $program->name : '';
        
        // Build validation rules
        $rules = [
            'school_id' => ['required', Rule::unique('student_faculty', 'school_id')->ignore($sf->id)],
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user ? $user->id : null)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user ? $user->id : null)],
            'role_type' => 'required|in:student,faculty',
            'program_id' => 'required|exists:programs,id',
            'yrlvl' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date|before_or_equal:today|after:1900-01-01',
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
            ],
        ];
        
        // Course is not required for Junior High School
        if ($programName === 'Junior High School') {
            $rules['course'] = 'nullable|string|max:255';
        } else {
            $rules['course'] = 'nullable|string|max:255';
        }
        
        $request->validate($rules, [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).',
            'birthdate.before_or_equal' => 'Birthdate cannot be in the future.',
            'birthdate.after' => 'Birthdate must be after January 1, 1900.'
        ]);

        $sf->school_id = $request->school_id;
        $sf->first_name = $request->first_name;
        $sf->last_name = $request->last_name;
        $sf->role = $request->role_type;
        $sf->program_id = $request->program_id;
        if ($request->role_type === 'student') {
            $sf->course = $request->course;
            $sf->yrlvl = $request->yrlvl;
        } else {
            $sf->course = null;
            $sf->yrlvl = null;
        }
        $sf->birthdate = $request->birthdate;
        $sf->save();

        if ($user) {
            $user->email = $request->email;
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->username = $request->username;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
        }

        // Redirect back to the previous page if provided, else to appropriate tab
        $redirectType = $request->input('role_type', $sf->role);
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Student/Faculty updated successfully!')
            : redirect()->route('user.management', ['type' => $redirectType])->with('success', 'Student/Faculty updated successfully!');
    }
}
