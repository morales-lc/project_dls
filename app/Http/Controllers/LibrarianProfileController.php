<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class LibrarianProfileController extends Controller
{
    /**
     * Show the logged-in librarian's profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('librarian.profile.edit', compact('user'));
    }

    /**
     * Update profile information and optionally password.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
        ];

        // If user provided a new password, require current_password and confirmation
        if ($request->filled('password')) {
            $rules['current_password'] = ['required'];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // Prepare update payload
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'] ?? $user->username,
            'contact_number' => $validated['contact_number'] ?? $user->contact_number,
            'address' => $validated['address'] ?? $user->address,
        ];

        // If changing password, verify current password then include new hashed password
        if ($request->filled('password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match our records.'])->withInput();
            }
            $updateData['password'] = Hash::make($request->input('password'));
        }

        // Perform update using query builder style to satisfy static analyzers
        User::where('id', $user->id)->update($updateData);

        return redirect()->route('librarian.profile')->with('success', 'Profile updated successfully.');
    }
}
