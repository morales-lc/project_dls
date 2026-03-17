<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LibraryStaff;
use App\Models\AlertBook;
use App\Models\AlertDepartment;

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
        // Fetch 3 random alert covers for the Senior Highschool department
        $covers = collect();
        try {
            $dept = AlertDepartment::where('name', 'Senior Highschool')->first();
            if ($dept) {
                $covers = AlertBook::where('department_id', $dept->id)
                    ->whereNotNull('cover_image')
                    ->inRandomOrder()
                    ->take(3)
                    ->get();
            }
        } catch (\Exception $e) {
            // silently ignore and keep $covers empty
        }

        return view('libraries.' . $department, compact('staff', 'covers'));
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
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middlename' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'role' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+-]+@lccdo\.edu\.ph$/'],
            'description' => 'nullable|string|max:1000',
            'department' => 'required|in:college,graduate,senior_high,ibed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            'middlename.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            'email.regex' => 'Email must be a valid @lccdo.edu.ph address.',
            'photo.max' => 'Photo size must not exceed 5MB.',
        ]);
        
        // Check if trying to add Library Coordinator to college department
        if ($validated['role'] === 'Library Coordinator' && $validated['department'] === 'college') {
            $existingCoordinator = LibraryStaff::where('role', 'Library Coordinator')
                                               ->where('department', 'college')
                                               ->exists();
            if ($existingCoordinator) {
                return redirect()->back()
                    ->withErrors(['role' => 'A Library Coordinator already exists for the College Library. Only one Library Coordinator is allowed per department.'])
                    ->withInput();
            }
        }
        
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
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'middlename' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'role' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+-]+@lccdo\.edu\.ph$/'],
            'description' => 'nullable|string|max:1000',
            'department' => 'required|in:college,graduate,senior_high,ibed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens, and apostrophes.',
            'middlename.regex' => 'Middle name can only contain letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens, and apostrophes.',
            'email.regex' => 'Email must be a valid @lccdo.edu.ph address.',
            'photo.max' => 'Photo size must not exceed 5MB.',
        ]);
        
        // Check if trying to change role to Library Coordinator for college department
        if ($validated['role'] === 'Library Coordinator' && $validated['department'] === 'college') {
            $existingCoordinator = LibraryStaff::where('role', 'Library Coordinator')
                                               ->where('department', 'college')
                                               ->where('id', '!=', $id)
                                               ->exists();
            if ($existingCoordinator) {
                return redirect()->back()
                    ->withErrors(['role' => 'A Library Coordinator already exists for the College Library. Only one Library Coordinator is allowed per department.'])
                    ->withInput();
            }
        }
        
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
