<?php

namespace App\Http\Controllers;

use App\Models\ELibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ELibraryController extends Controller
{
    /**
     * Public listing page.
     */
    public function index()
    {
        $libraries = ELibrary::orderBy('name')->get();
        return view('e-libraries.index', compact('libraries'));
    }

    /**
     * Management list for admin/librarian.
     */
    public function manage(Request $request)
    {
        $q = ELibrary::query();

        if ($search = $request->input('search')) {
            $q->where(function($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('instructions', 'like', "%{$search}%");
            });
        }

        if ($creds = $request->input('credentials')) {
            if ($creds === 'with') {
                $q->where(function ($w) {
                    $w->where(function ($x) {
                        $x->whereNotNull('username')->where('username', '!=', '');
                    })->orWhere(function ($x) {
                        $x->whereNotNull('password')->where('password', '!=', '');
                    });
                });
            } elseif ($creds === 'without') {
                $q->where(function ($w) {
                    $w->where(function ($x) {
                        $x->whereNull('username')->orWhere('username', '=','');
                    })->where(function ($x) {
                        $x->whereNull('password')->orWhere('password', '=','');
                    });
                });
            }
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        if (!in_array($sort, ['created_at','updated_at','name'])) $sort = 'created_at';
        if (!in_array($direction, ['asc','desc'])) $direction = 'desc';

        $libraries = $q->orderBy($sort, $direction)->paginate(15)->withQueryString();
        return view('e-libraries.manage', compact('libraries'));
    }

    public function create()
    {
        return view('e-libraries.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'link' => ['required', 'url', 'max:2048'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
        ], [
            'name.required' => 'The e-library name is required.',
            'name.max' => 'The name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'link.required' => 'The URL link is required.',
            'link.url' => 'Please provide a valid URL (e.g., https://example.com).',
            'link.max' => 'The URL cannot exceed 2048 characters.',
            'instructions.max' => 'The instructions cannot exceed 5000 characters.',
            'username.max' => 'The username cannot exceed 255 characters.',
            'password.max' => 'The password cannot exceed 255 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
            'image.max' => 'The image size cannot exceed 2MB.',
        ]);
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('elibraries', 'public');
            $data['image'] = $path;
        }

        ELibrary::create($data);

        return redirect()->route('e-libraries.manage')->with('status', 'E-Library created.');
    }

    public function edit($id)
    {
        $library = ELibrary::findOrFail($id);
        return view('e-libraries.edit', compact('library'));
    }

    public function update(Request $request, $id)
    {
        $library = ELibrary::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'link' => ['required', 'url', 'max:2048'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
        ], [
            'name.required' => 'The e-library name is required.',
            'name.max' => 'The name cannot exceed 255 characters.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'link.required' => 'The URL link is required.',
            'link.url' => 'Please provide a valid URL (e.g., https://example.com).',
            'link.max' => 'The URL cannot exceed 2048 characters.',
            'instructions.max' => 'The instructions cannot exceed 5000 characters.',
            'username.max' => 'The username cannot exceed 255 characters.',
            'password.max' => 'The password cannot exceed 255 characters.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
            'image.max' => 'The image size cannot exceed 2MB.',
        ]);
        
        if ($request->hasFile('image')) {
            if ($library->image && Storage::disk('public')->exists($library->image)) {
                Storage::disk('public')->delete($library->image);
            }
            $path = $request->file('image')->store('elibraries', 'public');
            $data['image'] = $path;
        }

        $library->update($data);

        return redirect()->route('e-libraries.manage')->with('status', 'E-Library updated.');
    }

    public function destroy($id)
    {
        $library = ELibrary::findOrFail($id);
        if ($library->image && Storage::disk('public')->exists($library->image)) {
            Storage::disk('public')->delete($library->image);
        }
        $library->delete();

        return redirect()->route('e-libraries.manage')->with('status', 'E-Library deleted.');
    }
}
