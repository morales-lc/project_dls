<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StudentFaculty;

class UserManagementController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'school_id' => 'required|regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/|unique:student_faculty,school_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'yrlvl' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        // Create user in users table
        $user = new \App\Models\User();
        $user->email = $request->email;
        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->save();

        // Create in student_faculty table
    $sf = new \App\Models\StudentFaculty();
    $sf->user_id = $user->id;
    $sf->school_id = $request->school_id;
    $sf->first_name = $request->first_name;
    $sf->last_name = $request->last_name;
    $sf->username = $request->username;
    $sf->role = $request->role;
    $sf->course = $request->course;
    $sf->yrlvl = $request->yrlvl;
    $sf->department = $request->department;
    $sf->birthdate = $request->birthdate;
    $sf->save();

        return redirect()->route('user.management')->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_id' => 'required|regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/|unique:student_faculty,school_id,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'yrlvl' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
        ]);

        $sf = \App\Models\StudentFaculty::findOrFail($id);
    $sf->school_id = $request->school_id;
    $sf->first_name = $request->first_name;
    $sf->last_name = $request->last_name;
    $sf->username = $request->username;
    $sf->role = $request->role;
    $sf->course = $request->course;
    $sf->yrlvl = $request->yrlvl;
    $sf->department = $request->department;
    $sf->birthdate = $request->birthdate;
    $sf->save();

        // Update email in users table
        if ($sf->user) {
            $sf->user->email = $request->email;
            $sf->user->save();
        }

        return redirect()->route('user.management')->with('success', 'User updated successfully!');
    }

    public function delete($id)
    {
        Log::info('UserManagementController@delete called for id: ' . $id);
        $sf = \App\Models\StudentFaculty::findOrFail($id);
        if ($sf->user) {
            Log::info('Deleting related user with id: ' . $sf->user->id);
            $sf->user->delete();
        }
        $sf->delete();
        Log::info('Deleted student_faculty record with id: ' . $id);
        return redirect()->route('user.management')->with('success', 'User deleted successfully!');
    }

    public function index()
    {
        $users = StudentFaculty::with('user')->get();
        return view('user-management', compact('users'));
    }
}
