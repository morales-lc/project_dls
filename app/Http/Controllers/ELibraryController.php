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
            'description' => ['nullable', 'string'],
            'link' => ['required', 'url', 'max:2048'],
            'instructions' => ['nullable', 'string'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
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
            'description' => ['nullable', 'string'],
            'link' => ['required', 'url', 'max:2048'],
            'instructions' => ['nullable', 'string'],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
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
