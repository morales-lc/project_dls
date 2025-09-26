<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibraryStaff;

class LibraryStaffController extends Controller
{
    // List all staff for management
    public function manage()
    {
        $staff = LibraryStaff::all();
        return view('libraries.manage-staff', compact('staff'));
    }

    // List staff by department (for public view)
    public function index($department)
    {
        $staff = LibraryStaff::where('department', $department)->get();
        return view('libraries.' . $department, compact('staff'));
    }

    public function create()
    {
        return view('libraries.add-staff');
    }

    public function edit($id)
    {
        $staff = LibraryStaff::findOrFail($id);
        return view('libraries.edit-staff', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:10',
            'first_name' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'department' => 'required|in:college,graduate,senior_high,ibed',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('library_staff', 'public');
        }
        LibraryStaff::create($validated);
        return redirect()->route('libraries.staff.manage')->with('success', 'Staff added successfully!');
    }

    public function update(Request $request, $id)
    {
        $staff = LibraryStaff::findOrFail($id);
        $validated = $request->validate([
            'prefix' => 'required|string|max:10',
            'first_name' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'department' => 'required|in:college,graduate,senior_high,ibed',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('library_staff', 'public');
        }
        $staff->update($validated);
        return redirect()->route('libraries.staff.manage')->with('success', 'Staff updated successfully!');
    }

    public function destroy($id)
    {
        $staff = LibraryStaff::findOrFail($id);
        if ($staff->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($staff->photo);
        }
        $staff->delete();
        return redirect()->route('libraries.staff.manage')->with('success', 'Staff deleted successfully!');
    }
}
