<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StudentFaculty;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class UserManagementController extends Controller
{
    public function add(Request $request)
    {
        $returnUrl = $request->input('return_url');
        $role = $request->input('role');
        if ($role === 'student_faculty') {
            $request->validate([
                'school_id' => 'required|unique:student_faculty,school_id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'role_type' => 'required|in:student,faculty',
                'program_id' => 'required|exists:programs,id',
                'course' => 'nullable|string|max:255',
                'yrlvl' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date',
                'password' => 'nullable|string|min:6',
            ]);

            // Create user in users table
            $user = new \App\Models\User();
            $user->email = $request->email;
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->username = $request->username;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Create in student_faculty table
            $sf = new \App\Models\StudentFaculty();
            $sf->user_id = $user->id;
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

            return $returnUrl
                ? redirect($returnUrl)->with('success', 'Student/Faculty added successfully!')
                : redirect()->route('user.management')->with('success', 'Student/Faculty added successfully!');
        } elseif (in_array($role, ['admin', 'librarian', 'guest'])) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'contact_number' => 'nullable|digits:11',
                'address' => 'nullable|string|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
                ],
                'role' => 'required|in:admin,librarian,guest',
            ]);

            $user = new \App\Models\User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->contact_number = $request->contact_number;
            $user->address = $request->address;
            $user->password = bcrypt($request->password);
            $user->role = $role;
            if ($role === 'guest') {
                // Store encrypted plaintext for email purposes
                $user->guest_plain_password = Crypt::encryptString($request->password);
            }
            $user->save();

            return $returnUrl
                ? redirect($returnUrl)->with('success', ucfirst($role) . ' added successfully!')
                : redirect()->route('user.management', ['type' => $role])->with('success', ucfirst($role) . ' added successfully!');
        } else {
            return redirect()->route('user.management')->with('error', 'Invalid user type.');
        }
    }

    public function update(Request $request, $id)
    {
        $returnUrl = $request->input('return_url');
        // Determine whether the id belongs to a student_faculty or a user (admin/librarian)
        $sf = StudentFaculty::find($id);

        if ($sf) {
            // Updating a student/faculty record
            $userId = $sf->user ? $sf->user->id : null;

            $request->validate([
                'school_id' => ['required', Rule::unique('student_faculty', 'school_id')->ignore($sf->id)],
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
                'role' => 'required|string|max:255',
                'course' => 'nullable|string|max:255',
                'yrlvl' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date',
                'password' => 'nullable|string|min:6',
            ]);

            $sf->school_id = $request->school_id;
            $sf->first_name = $request->first_name;
            $sf->last_name = $request->last_name;
            $sf->role = $request->role;
            $sf->course = $request->course;
            $sf->yrlvl = $request->yrlvl;
            $sf->department = $request->department;
            $sf->birthdate = $request->birthdate;

            $sf->save();

            if ($sf->user) {
                $sf->user->email = $request->email;
                $sf->user->username = $request->username;
                if ($request->filled('password')) {
                    $sf->user->password = bcrypt($request->password);
                }
                $sf->user->save();
            }

            // Redirect back to previous page if provided
            return $returnUrl
                ? redirect($returnUrl)->with('success', 'Student/Faculty updated successfully!')
                : redirect()->back()->with('success', 'Student/Faculty updated successfully!');
        }

    // Otherwise, try to update a User (admin, librarian, or guest)
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'contact_number' => 'nullable|digits:11',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:admin,librarian,guest',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->contact_number = $request->contact_number;
        $user->address = $request->address;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            // If guest, keep encrypted plaintext for email purposes
            if ($request->role === 'guest') {
                $user->guest_plain_password = Crypt::encryptString($request->password);
            }
        }

        // If role is changed away from guest, clear the encrypted guest password
        if ($request->role !== 'guest') {
            $user->guest_plain_password = null;
        }

        $user->save();

        return $returnUrl
            ? redirect($returnUrl)->with('success', ucfirst($user->role) . ' updated successfully!')
            : redirect()->back()->with('success', ucfirst($user->role) . ' updated successfully!');
    }

    public function delete(Request $request, $id)
    {
        Log::info('UserManagementController@delete called for id: ' . $id);
        $sf = \App\Models\StudentFaculty::find($id);
        if ($sf) {
            if ($sf->user) {
                Log::info('Deleting related user with id: ' . $sf->user->id);
                $sf->user->delete();
            }
            $sf->delete();
            Log::info('Deleted student_faculty record with id: ' . $id);
            $returnUrl = $request->input('return_url');
            return $returnUrl
                ? redirect($returnUrl)->with('success', 'User deleted successfully!')
                : redirect()->back()->with('success', 'User deleted successfully!');
        }

        $user = \App\Models\User::findOrFail($id);
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->id == $user->id && $user->role === 'admin') {
            return redirect()->route('user.management', ['type' => 'admin'])->with('error', 'You cannot delete your own admin account while logged in.');
        }
        if ($user->role === 'admin') {
            $adminCount = \App\Models\User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return redirect()->route('user.management', ['type' => 'admin'])->with('error', 'There must be at least one admin account at all times.');
            }
        }
        $user->delete();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', ucfirst($user->role) . ' deleted successfully!')
            : redirect()->back()->with('success', ucfirst($user->role) . ' deleted successfully!');
    }

    public function index(Request $request)
    {
    $type = $request->query('type', 'student');
        $search = $request->query('q');
        $schoolId = $request->query('school_id');
        $hasSearch = !empty($search);
        $hasSchoolId = !empty($schoolId);

        if ($type === 'student' || $type === 'faculty') {
            $usersQuery = \App\Models\StudentFaculty::with('user')->where('role', $type);
            if ($hasSearch) {
                $s = $search;
                $usersQuery->where(function ($q) use ($s) {
                    $q->where('first_name', 'like', "%{$s}%")
                      ->orWhere('last_name', 'like', "%{$s}%")
                      ->orWhere('username', 'like', "%{$s}%");
                })->orWhereHas('user', function ($q2) use ($s) {
                    $q2->where('email', 'like', "%{$s}%");
                });
            }
            if ($hasSchoolId) {
                $sid = $schoolId;
                $usersQuery->where('school_id', 'like', "%{$sid}%");
            }
            $users = $usersQuery->get();
        } elseif (in_array($type, ['admin', 'librarian', 'guest'])) {
            $usersQuery = \App\Models\User::where('role', $type);
            if ($hasSearch) {
                $s = $search;
                $usersQuery->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                      ->orWhere('username', 'like', "%{$s}%");
                });
            }
            $users = $usersQuery->get();
        } else {
            $users = collect();
        }

        return view('user-management', compact('users', 'type', 'search', 'schoolId'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'student_faculty');
        if ($type === 'student_faculty') {
            return view('users.create-student');
        } elseif ($type === 'admin') {
            return view('users.create-admin');
        } elseif ($type === 'librarian') {
            return view('users.create-librarian');
        } elseif ($type === 'guest') {
            return view('users.create-guest');
        }
        abort(404);
    }
}
