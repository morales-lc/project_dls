<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\StudentFaculty;

class UserManagementController extends Controller
{
    public function add(Request $request)
    {
        $role = $request->input('role');
        if ($role === 'student_faculty') {
            $request->validate([
                'school_id' => 'required|regex:/^[A-Z]{1,2}[0-9]{2}-[0-9]{4}$/|unique:student_faculty,school_id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'username' => 'required|string|max:255|unique:student_faculty,username',
                'role' => 'required|in:student_faculty',
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

            return redirect()->route('user.management')->with('success', 'Student/Faculty added successfully!');
        } elseif (in_array($role, ['admin', 'librarian'])) {
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
                'role' => 'required|in:admin,librarian',
            ]);

            $user = new \App\Models\User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->contact_number = $request->contact_number;
            $user->address = $request->address;
            $user->password = bcrypt($request->password);
            $user->role = $role;
            $user->save();

            return redirect()->route('user.management', ['type' => $role])->with('success', ucfirst($role) . ' added successfully!');
        } else {
            return redirect()->route('user.management')->with('error', 'Invalid user type.');
        }
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

    public function index(Request $request)
    {
        $type = $request->query('type', 'student_faculty');
    $search = $request->query('q');
    $schoolId = $request->query('school_id');
    $hasSearch = !empty($search);
    $hasSchoolId = !empty($schoolId);

        if ($type === 'student_faculty') {
            $usersQuery = \App\Models\StudentFaculty::with('user');
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
        } elseif (in_array($type, ['admin', 'librarian'])) {
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

    /**
     * Show create form for different user types.
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'student_faculty');
        if ($type === 'student_faculty') {
            return view('users.create-student');
        } elseif ($type === 'admin') {
            return view('users.create-admin');
        } elseif ($type === 'librarian') {
            return view('users.create-librarian');
        }
        abort(404);
    }
}
