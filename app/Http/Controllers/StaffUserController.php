<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class StaffUserController extends Controller
{
    /**
     * Show listing for admins or librarians depending on query param `type`.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'admin');
        $search = $request->query('q');

        $usersQuery = User::where('role', $type);
        if (!empty($search)) {
            $s = $search;
            $usersQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('username', 'like', "%{$s}%");
            });
        }

        $users = $usersQuery->get();

        return view('user-management', compact('users', 'type', 'search'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'admin');
        if ($type === 'admin') return view('users.create-admin');
        if ($type === 'librarian') return view('users.create-librarian');
        abort(404);
    }

    public function add(Request $request)
    {
        $role = $request->input('role');
        if (!in_array($role, ['admin', 'librarian'])) {
            return redirect()->route('staff.management')->with('error', 'Invalid role');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'contact_number' => 'nullable|digits:11',
            'address' => 'nullable|string|max:255',
            'password' => ['required', 'string', 'min:6'],
            'role' => 'required|in:admin,librarian',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->contact_number = $request->contact_number;
        $user->address = $request->address;
        $user->password = bcrypt($request->password);
        $user->role = $role;
        $user->save();

        return redirect()->route('staff.management', ['type' => $role])->with('success', ucfirst($role) . ' added successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'contact_number' => 'nullable|digits:11',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:admin,librarian',
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
        }

        $user->save();

        return redirect()->route('staff.management', ['type' => $user->role])->with('success', ucfirst($user->role) . ' updated successfully!');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('staff.management')->with('success', 'User deleted successfully!');
    }
}
