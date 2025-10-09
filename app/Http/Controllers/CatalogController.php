<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;

class CatalogController extends Controller
{
    public function index()
    {
        $catalogs = Catalog::orderBy('title')->paginate(12);
        return view('catalogs.index', compact('catalogs'));
    }

    public function create()
    {
        return view('catalogs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'author' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:100',
            'sublocation' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'edition' => 'nullable|string|max:100',
            'format' => 'nullable|string|max:255',
            'lccn' => 'nullable|string|max:100',
            'isbn' => 'nullable|string|max:100',
            'issn' => 'nullable|string|max:100',
            'series' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string',
        ]);

        $catalog = Catalog::create($data);

        return redirect()
            ->route('catalogs.create')
            ->with('success', 'Catalog item added successfully.');
    }

    public function show($id)
    {
        $catalog = Catalog::findOrFail($id);
        // Simple recommendations: prefer same author, fallback to same format
        $recommendations = Catalog::where('id', '!=', $catalog->id)
            ->when($catalog->author, function($q) use ($catalog) {
                return $q->where('author', $catalog->author);
            })
            ->orWhere(function($q) use ($catalog) {
                if ($catalog->format) {
                    $q->where('format', $catalog->format);
                }
            })
            ->orderBy('title')
            ->limit(9)
            ->get();

        return view('catalogs.show', compact('catalog', 'recommendations'));
    }


    public function search(Request $request)
    {
        $query = Catalog::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('publisher', 'like', "%{$q}%")
                    ->orWhere('additional_info', 'like', "%{$q}%");
            });
        }

        // Apply type filter when provided
        if ($request->filled('type')) {
            $type = $request->input('type');
            // Assume there's a 'format' or 'type' column in catalogs table; try both
            $query->where(function($sub) use ($type) {
                $sub->where('format', $type)
                    ->orWhere('type', $type);
            });
        }

        // Paginate and keep query parameters in pagination links
        $perPage = 10;
        $catalogs = $query->orderBy('title')->paginate($perPage)->appends($request->except('page'));

        return view('catalogs.search', compact('catalogs'));
    }
}
