<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlertBook;
use App\Models\AlertDepartment;
use Illuminate\Support\Facades\Storage;

class AlertServiceController extends Controller
{
    public function group($year, $month, $group, $value)
    {
        $decodedName = urldecode($value);
        $department = AlertDepartment::where('name', $decodedName)->where('type', $group)->first();
        $books = $department
            ? AlertBook::where('year', $year)->where('month', $month)->where('department_id', $department->id)->get()
            : collect();
        $displayName = $decodedName;
        $displayMonth = \DateTime::createFromFormat('!m', $month)->format('F');
        return view('alert-services.group', compact('books', 'displayName', 'displayMonth', 'year'));
    }

    public function manage(Request $request)
    {
        $query = AlertBook::with('department');
        $departments = AlertDepartment::all();

        // Filtering
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('year', 'like', "%$search%");
            });
        }
        if ($request->filled('department')) {
            $query->where('department_id', $request->input('department'));
        }

        // Sorting
        $sort = $request->input('sort', 'year');
        $direction = $request->input('direction', 'desc');
        $query->orderBy($sort, $direction);
        if ($sort !== 'year') {
            $query->orderByDesc('year'); // secondary sort by year
        }

        $books = $query->paginate(10)->appends($request->except('page'));
        return view('alert-services.manage', compact('books', 'departments'));
    }

    public function edit($id)
    {
        $book = AlertBook::findOrFail($id);
        $departments = AlertDepartment::all();
        return view('alert-services.edit', compact('book', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $book = AlertBook::findOrFail($id);
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'department_id' => 'required|exists:alert_departments,id',
        ]);
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('alert_books', 'public');
            $book->pdf_path = $pdfPath;
        }
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('alert_books/covers', 'public');
            $book->cover_image = $coverPath;
        }
        $book->call_number = $request->input('call_number');
    $book->author = $request->input('author');
        $book->title = $request->input('title');
        $book->department_id = $request->input('department_id');
        $book->month = $request->input('month');
        $book->year = $request->input('year');
        $book->save();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Book updated successfully!')
            : redirect()->route('alert-services.manage')->with('success', 'Book updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $book = AlertBook::findOrFail($id);
        $book->delete();
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Book deleted successfully!')
            : redirect()->back()->with('success', 'Book deleted successfully!');
    }
    public function index(Request $request)
    {
        $months = AlertBook::select('month', 'year')
            ->distinct()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();
        $departments = AlertDepartment::all();
        $books = AlertBook::all();
        return view('alert-services.index', compact('months', 'departments', 'books'));
    }

    public function create()
    {
        $departments = AlertDepartment::all();
        return view('alert-services.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'call_number' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'department_id' => 'required|exists:alert_departments,id',
        ]);
        $pdfPath = $request->file('pdf_file')->store('alert_books', 'public');
        $coverPath = $request->file('cover_image') ? $request->file('cover_image')->store('alert_books/covers', 'public') : null;
        AlertBook::create([
            'title' => $request->input('title'),
            'call_number' => $request->input('call_number'),
            'author' => $request->input('author'),
            'pdf_path' => $pdfPath,
            'cover_image' => $coverPath,
            'department_id' => $request->input('department_id'),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
        ]);
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Book posted successfully!')
            : redirect()->route('alert-services.manage')->with('success', 'Book posted successfully!');
    }
}
