<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::orderBy('title')->paginate(12);
        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'nullable|string|max:512',
            'description' => 'nullable|string',
            'call_number' => 'nullable|string|max:255',
            'sublocation' => 'nullable|string|max:255',
            'published' => 'nullable|string|max:255',
            'copyright' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'media_type' => 'nullable|string|max:255',
            'carrier_type' => 'nullable|string|max:255',
            'issn' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'lccn' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string',
        ]);

        $book = Book::create($data);

        return redirect()->route('books.create', $book->id)->with('success', 'Book added successfully');
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('books.show', compact('book'));
    }
}
