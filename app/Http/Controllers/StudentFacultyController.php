<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentFaculty;
use App\Models\User;
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

        $request->validate([
            'school_id' => ['required', 'regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/', Rule::unique('student_faculty', 'school_id')->ignore($sf->id)],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user ? $user->id : null)],
            'role_type' => 'required|in:student,faculty',
            'program_id' => 'required|exists:programs,id',
            'course' => 'nullable|string|max:255',
            'yrlvl' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
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
        // No longer manage username/password here; done on users elsewhere
        $sf->save();

        if ($user) {
            $user->email = $request->email;
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->save();
        }

        // Redirect back to the previous page if provided, else to appropriate tab
        $redirectType = $request->input('role_type', $sf->role);
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Student/Faculty updated successfully!')
            : redirect()->route('user.management', ['type' => $redirectType])->with('success', 'Student/Faculty updated successfully!');
    }
}
