<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\LiraRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\LiraSubmitted;
use App\Mail\LiraDecision;
use Illuminate\Support\Facades\Config;

class LiRAController extends Controller
{
    public function showForm(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('status', 'Please log in to request via LiRA.');
        }
        $sf = $user->studentFaculty ?? null;
        $first = $sf->first_name ?? '';
        $middle = $sf->middle_name ?? '';
        $last = $sf->last_name ?? '';
        $email = $user->email ?? '';
        $department = $sf->department ?? '';
        $course = $sf->course ?? '';
        $yrlvl = $sf->yrlvl ?? '';
        $programStrandGradeLevel = trim($course . ($yrlvl ? '-' . $yrlvl : '')) ?: 'BSSW-4';
        $designationRaw = $sf->role ?? 'Faculty';
        $designation = ucfirst(strtolower($designationRaw));

        // Get catalog info from query parameters
        $title = $request->query('title', '');
        $author = $request->query('author', '');
        $call_number = $request->query('call_number', '');
        $isbn = $request->query('isbn', '');
        $lccn = $request->query('lccn', '');
        $issn = $request->query('issn', '');

        // Compose examplePurposive: Title, Author, Call number, LCCN/ISBN/ISSN
        $examplePurposive = '';
        $examplePurposive .= $title ? $title . ', ' : '';
        $examplePurposive .= $author ? $author . ', ' : '';
        $examplePurposive .= $call_number ? $call_number . ', ' : '';

        $idParts = [];
        if ($lccn) $idParts[] = "LCCN: $lccn";
        if ($isbn) $idParts[] = "ISBN: $isbn";
        if ($issn) $idParts[] = "ISSN: $issn";

        if (!empty($idParts)) {
            $examplePurposive .= implode(', ', $idParts);
        } else {
            $examplePurposive = rtrim($examplePurposive, ', ');
        }

        $action = $request->query('action', 'borrow');
        $whatKind = $action === 'scanning' ? 'Library Scanning' : 'Book Borrowing';
        $whatType = $action === 'scanning' ? 'Scanning' : 'Books';

        // Build a default prefill for the "For BOOK BORROWING and LIBRARY SCANNING" textarea
        // Format: "Title, Author, Call Number"
        $forBorrowScan = trim(implode(', ', array_filter([
            $title ?: null,
            $author ?: null,
            $call_number ?: null,
        ])));

        // Prepare data for our internal LIRA form view
        $prefill = [
            'first' => $first,
            'middle' => $middle,
            'last' => $last,
            'email' => $email,
            'department' => $department,
            'programstrandgradeLevel' => $programStrandGradeLevel,
            'designation' => $designation,
            'action' => $request->query('action', 'borrow'),
            'title' => $title,
            'author' => $author,
            'call_number' => $call_number,
            'isbn' => $isbn,
            'lccn' => $lccn,
            'issn' => $issn,
            'for_borrow_scan' => $forBorrowScan,
        ];

        return view('lira.form', compact('prefill'));
    }

    public function submit(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'consent' => 'accepted',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'action' => 'nullable|in:borrow,scanning',
            'assistance_types' => 'nullable|array',
            'assistance_types.*' => 'string|max:255',
            'resource_types' => 'nullable|array',
            'resource_types.*' => 'string|max:255',
            'for_videos' => 'nullable|array',
            'for_videos.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // normalize array inputs to ensure consistent storage
        $assistance = $request->input('assistance_types', []);
        if ($assistance === null) $assistance = [];
        if (!is_array($assistance)) $assistance = [$assistance];

        $resource = $request->input('resource_types', []);
        if ($resource === null) $resource = [];
        if (!is_array($resource)) $resource = [$resource];

        $videos = $request->input('for_videos', []);
        if ($videos === null) $videos = [];
        if (!is_array($videos)) $videos = [$videos];

        $lira = LiraRequest::create([
            'user_id' => Auth::id(),
            'consent' => true,
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'program_strand_grade_level' => $request->input('program_strand_grade_level'),
            'designation' => $request->input('designation'),
            'department' => $request->input('department'),
            'action' => $request->input('action'),
            // store as JSON strings to avoid Array-to-string conversion at DB layer
            'assistance_types' => json_encode($assistance),
            'resource_types' => json_encode($resource),
            'titles_of' => $request->input('titles_of'),
            // form field name is `for_borrow_scan` in the view
            'for_borrow_scan' => $request->input('for_borrow_scan'),
            'for_list' => $request->input('for_list'),
            'for_videos' => json_encode($videos),
        ]);

        // Send notification to librarian/staff
        $librarian = env('ALINET_LIBRARIAN_EMAIL', null);
        if ($librarian) {
            Mail::to($librarian)->send(new LiraSubmitted($lira));
        }

        return redirect()->route('lira.form')->with('status', 'Your request was submitted. Thank you!');
    }

    // Admin listing and filtering
    public function index(Request $request)
    {
        $q = LiraRequest::query();
        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }
        if ($request->filled('email')) {
            $q->where('email', 'like', '%'.$request->input('email').'%');
        }

        $items = $q->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('lira.manage', compact('items'));
    }

    // Accept or reject
    public function decide(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:accepted,rejected',
            'decision_reason' => 'required_if:decision,rejected|nullable|string|max:2000',
        ]);
        $lira = LiraRequest::findOrFail($id);

        // prevent changing decision if already processed
        if (!empty($lira->status) && $lira->status !== 'pending') {
            return redirect()->back()->with('status', 'This request has already been processed.');
        }

        $lira->status = $request->input('decision');
        $lira->processed_by = Auth::id();
        $lira->processed_at = now();
        // store reason only for rejected; clear otherwise
        $lira->decision_reason = $lira->status === 'rejected' ? $request->input('decision_reason') : null;
        $lira->save();

        // notify requester
        Mail::to($lira->email)->send(new LiraDecision($lira, $lira->status, $lira->decision_reason));

        return redirect()->back()->with('status', 'Decision recorded.');
    }

    //delete a LiRA request
    public function destroy(Request $request, $id)
    {
        $lira = LiraRequest::findOrFail($id);

        

        try {
            $lira->delete();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete request.'], 500);
            }
            return redirect()->back()->with('status', 'Failed to delete request.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Deleted', 'id' => $id]);
        }

        return redirect()->route('lira.manage')->with('status', 'Request deleted.');
    }
}
