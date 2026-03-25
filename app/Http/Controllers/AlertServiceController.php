<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlertBook;
use App\Models\AlertDepartment;
use Illuminate\Support\Facades\Storage;

/**
 * Alert Service Controller
 * 
 * Manages the Alert Services feature which provides monthly book notifications
 * organized by departments. Books are categorized by year, month, and department
 * type (e.g., program, department, etc.).
 * 
 * @package App\Http\Controllers
 */
class AlertServiceController extends Controller
{
    /**
     * Display books for a specific group (department) in a given month/year
     * 
     * Shows all alert books belonging to a specific department category for a given
     * time period. Also includes bookmark status for authenticated users.
     * 
     * @param int $year The year (e.g., 2025)
     * @param int $month The month number (1-12)
     * @param string $group The department type/group
     * @param string $value URL-encoded department name
     * @return \Illuminate\View\View
     */
    public function group($year, $month, $group, $value)
    {
        // Decode URL-encoded department name
        $decodedName = urldecode($value);
        
        // Find the department by name and type
        $department = AlertDepartment::where('name', $decodedName)->where('type', $group)->first();
        
        // Get all books for this department in the specified month/year
        $books = $department
            ? AlertBook::where('year', $year)->where('month', $month)->where('department_id', $department->id)->get()
            : collect();
        
        // Prepare display values
        $displayName = $decodedName;
        $displayMonth = \DateTime::createFromFormat('!m', $month)->format('F');
        
        // Collect bookmarked AlertBook IDs for the authenticated Student/Faculty user
        $bookmarkedIds = [];
        $cartIds = [];
        try {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $sf = $user->studentFaculty ?? null;
                if ($sf) {
                    $bookmarkedIds = \App\Models\Bookmark::where('student_faculty_id', $sf->id)
                        ->where('bookmarkable_type', AlertBook::class)
                        ->pluck('bookmarkable_id')
                        ->toArray();

                    $cartIds = \App\Models\CartItem::where('student_faculty_id', $sf->id)
                        ->where('cartable_type', AlertBook::class)
                        ->pluck('cartable_id')
                        ->toArray();
                }
            }
        } catch (\Throwable $e) {
            $bookmarkedIds = [];
            $cartIds = [];
        }
        return view('alert-services.group', compact('books', 'displayName', 'displayMonth', 'year', 'bookmarkedIds', 'cartIds'));
    }

    /**
     * Display the management interface for alert books
     * 
     * Shows a paginated, filterable, and sortable list of all alert books.
     * Supports filtering by search term and department, and sorting by various fields.
     * 
     * @param Request $request HTTP request with optional query parameters:
     *                         - search: search term for title/year
     *                         - department: filter by department ID
     *                         - sort: field to sort by (default: year)
     *                         - direction: sort direction (asc/desc, default: desc)
     * @return \Illuminate\View\View
     */
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

    /**
     * Show the form for editing an alert book
     * 
     * @param int $id Alert book ID
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function edit($id)
    {
        $book = AlertBook::findOrFail($id);
        $departments = AlertDepartment::all();
        return view('alert-services.edit', compact('book', 'departments'));
    }

    /**
     * Update an existing alert book
     * 
     * Validates and updates the book information. Handles file uploads for
     * PDF files and cover images. Supports returning to a custom URL.
     * 
     * @param Request $request HTTP request with book data and optional files
     * @param int $id Alert book ID to update
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(Request $request, $id)
    {
        $book = AlertBook::findOrFail($id);
        
        // Validate incoming request data
        $validated = $request->validate([
            'title' => 'nullable|string|max:500',
            'call_number' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:500',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'department_id' => 'required|exists:alert_departments,id',
        ], [
            'title.max' => 'The title cannot exceed 500 characters.',
            'call_number.max' => 'The call number cannot exceed 100 characters.',
            'author.max' => 'The author name cannot exceed 500 characters.',
            'pdf_file.mimes' => 'The file must be a PDF document.',
            'pdf_file.max' => 'The PDF file size cannot exceed 10MB.',
            'cover_image.image' => 'The cover must be an image file.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif.',
            'cover_image.max' => 'The cover image size cannot exceed 2MB.',
            'month.required' => 'Please select a month.',
            'month.min' => 'Please select a valid month (1-12).',
            'month.max' => 'Please select a valid month (1-12).',
            'year.required' => 'The year is required.',
            'year.min' => 'The year must be at least 2000.',
            'year.max' => 'The year cannot exceed 2100.',
            'department_id.required' => 'Please select a department or category.',
            'department_id.exists' => 'The selected department is invalid.',
        ]);

        // Validate that the date is not in the future
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $inputYear = (int)$validated['year'];
        $inputMonth = (int)$validated['month'];

        if ($inputYear > $currentYear || ($inputYear == $currentYear && $inputMonth > $currentMonth)) {
            return back()->withErrors(['year' => 'The date cannot be in the future. Please select a month and year up to the current date.'])->withInput();
        }
        
        // Handle PDF file upload if provided
        if ($request->hasFile('pdf_file')) {
            $pdfPath = $request->file('pdf_file')->store('alert_books', 'public');
            $book->pdf_path = $pdfPath;
        }
        
        // Handle cover image upload if provided
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('alert_books/covers', 'public');
            $book->cover_image = $coverPath;
        }
        
        // Update book fields
        $book->call_number = $request->input('call_number');
    $book->author = $request->input('author');
        $book->title = $request->input('title');
        $book->department_id = $request->input('department_id');
        $book->month = $request->input('month');
        $book->year = $request->input('year');
        $book->save();
        
        // Redirect to custom URL if provided, otherwise return to management page
        $returnUrl = $request->input('return_url');
        return $returnUrl
            ? redirect($returnUrl)->with('success', 'Book updated successfully!')
            : redirect()->route('alert-services.manage')->with('success', 'Book updated successfully!');
    }

    /**
     * Delete an alert book
     * 
     * @param Request $request HTTP request (may contain return_url)
     * @param int $id Alert book ID to delete
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
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

    /**
     * Show the form for creating a new alert book
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = AlertDepartment::all();
        return view('alert-services.create', compact('departments'));
    }

    /**
     * Store a newly created alert book
     * 
     * Validates input, uploads PDF and optional cover image, and creates
     * a new alert book record in the database.
     * 
     * @param Request $request HTTP request with book data and files
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:500',
            'call_number' => 'nullable|string|max:100',
            'author' => 'nullable|string|max:500',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'department_id' => 'required|exists:alert_departments,id',
        ], [
            'title.max' => 'The title cannot exceed 500 characters.',
            'call_number.max' => 'The call number cannot exceed 100 characters.',
            'author.max' => 'The author name cannot exceed 500 characters.',
            'pdf_file.required' => 'A PDF file is required.',
            'pdf_file.mimes' => 'The file must be a PDF document.',
            'pdf_file.max' => 'The PDF file size cannot exceed 10MB.',
            'cover_image.image' => 'The cover must be an image file.',
            'cover_image.mimes' => 'The cover image must be a file of type: jpeg, png, jpg, gif.',
            'cover_image.max' => 'The cover image size cannot exceed 2MB.',
            'month.required' => 'Please select a month.',
            'month.min' => 'Please select a valid month (1-12).',
            'month.max' => 'Please select a valid month (1-12).',
            'year.required' => 'The year is required.',
            'year.min' => 'The year must be at least 2000.',
            'year.max' => 'The year cannot exceed 2100.',
            'department_id.required' => 'Please select a department or category.',
            'department_id.exists' => 'The selected department is invalid.',
        ]);

        // Validate that the date is not in the future
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('n');
        $inputYear = (int)$validated['year'];
        $inputMonth = (int)$validated['month'];

        if ($inputYear > $currentYear || ($inputYear == $currentYear && $inputMonth > $currentMonth)) {
            return back()->withErrors(['year' => 'The date cannot be in the future. Please select a month and year up to the current date.'])->withInput();
        }
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
