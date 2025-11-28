<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StudentFaculty;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserManagementExport;

class UserManagementController extends Controller
{
    public function add(Request $request)
    {
        $returnUrl = $request->input('return_url');
        $role = $request->input('role');
        if ($role === 'student_faculty') {
            $request->validate([
                'school_id' => 'required|unique:student_faculty,school_id',
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:users,username',
                'role_type' => 'required|in:student,faculty',
                'program_id' => 'required|exists:programs,id',
                'course' => 'nullable|string|max:255',
                'yrlvl' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date',
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
                ],
            ], [
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).'
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
        // If this request is NOT explicitly for a user role (admin/librarian/guest),
        // then and only then try updating a Student/Faculty record. This avoids
        // accidental collisions when a users.id matches a student_faculty.id.
        $sf = null;
        if (!$request->has('role')) {
            $sf = StudentFaculty::find($id);
        }

        if ($sf) {
            // Updating a student/faculty record
            $userId = $sf->user ? $sf->user->id : null;

            $request->validate([
                'school_id' => ['required', Rule::unique('student_faculty', 'school_id')->ignore($sf->id)],
                'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
                'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
                'role' => 'required|string|max:255',
                'course' => 'nullable|string|max:255',
                'yrlvl' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'birthdate' => 'nullable|date|before_or_equal:today|after:1900-01-01',
                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
                ],
            ], [
                'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
                'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
                'password.min' => 'Password must be at least 8 characters.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).'
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
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/'
            ],
            'guest_expires_at' => 'nullable|date',
            'guest_account_status' => 'nullable|in:active,expired',
        ], [
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&#).'
        ]);

        // Log incoming request data for debugging
        Log::info('Updating user ' . $id . ' with data:', [
            'guest_expires_at' => $request->guest_expires_at,
            'guest_account_status' => $request->guest_account_status,
            'role' => $request->role
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->contact_number = $request->contact_number;
        $user->address = $request->address;
        $user->role = $request->role;

        // Handle guest-specific fields
        if ($request->role === 'guest') {
            // Update expiration date - handle both filled and empty values
            if ($request->filled('guest_expires_at')) {
                $user->guest_expires_at = $request->guest_expires_at;
                Log::info('Setting guest_expires_at to: ' . $request->guest_expires_at);
            } elseif ($request->has('guest_expires_at') && empty($request->guest_expires_at)) {
                // Explicitly set to null if field exists but is empty
                $user->guest_expires_at = null;
                Log::info('Clearing guest_expires_at (set to null)');
            }
            
            // Update account status
            if ($request->filled('guest_account_status')) {
                $user->guest_account_status = $request->guest_account_status;
                Log::info('Setting guest_account_status to: ' . $request->guest_account_status);
            }
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            // If guest, keep encrypted plaintext for email purposes
            if ($request->role === 'guest') {
                $user->guest_plain_password = Crypt::encryptString($request->password);
            }
        }

        // If role is changed away from guest, clear the guest-related fields
        if ($request->role !== 'guest') {
            $user->guest_plain_password = null;
            $user->guest_expires_at = null;
            $user->guest_account_status = 'active';
        }

        Log::info('About to save user with:', [
            'guest_expires_at' => $user->guest_expires_at,
            'guest_account_status' => $user->guest_account_status
        ]);

        $user->save();
        
        Log::info('User saved. Reloading from DB...');
        $user->refresh();
        
        Log::info('After save - DB values:', [
            'guest_expires_at' => $user->guest_expires_at,
            'guest_account_status' => $user->guest_account_status
        ]);

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

    public function export()
    {
        return Excel::download(new UserManagementExport, 'users_' . date('Y-m-d_His') . '.xlsx');
    }
}
