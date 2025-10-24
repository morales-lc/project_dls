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
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LiRAExport;
use App\Exports\LiRAExportAllTabs;

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
    // Department removed from Student/Faculty profile; use Program + Course + Year Level
    $department = '';
    $programName = $sf?->program?->name ?? '';
    $course = $sf->course ?? '';
    $yrlvl = $sf->yrlvl ?? '';
    // Build: Program + Course (if any) + Year Level (if any), separated by " - "
    $psglParts = [];
    if (!empty($programName)) $psglParts[] = $programName;
    if (!empty($course)) $psglParts[] = $course;
    if (!empty($yrlvl)) $psglParts[] = $yrlvl;
    $programStrandGradeLevel = implode(' - ', $psglParts);
        $designationRaw = $sf->role ?? '';
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
            // Queue the email to avoid blocking the request
            Mail::to($librarian)->queue(new LiraSubmitted($lira));
        }

        return redirect()->route('lira.form')->with('status', 'Your request was submitted. Thank you!');
    }

    // Admin listing and filtering
    public function index(Request $request)
    {
        $q = LiraRequest::query();
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'awaiting_response') {
                // Accepted but not yet responded
                $q->where('status', 'accepted')->whereNull('response_sent_at');
            } else {
                $q->where('status', $status);
            }
        }
        if ($request->filled('email')) {
            $q->where('email', 'like', '%'.$request->input('email').'%');
        }

        // Date filtering
        $dateFilter = $request->input('date_filter');
        if ($dateFilter === 'today') {
            $q->whereDate('created_at', Carbon::today());
        } elseif ($dateFilter === 'month') {
            // Support either month_year=YYYY-MM or year + month params
            $monthYear = $request->input('month_year'); // legacy
            $yearParam = $request->input('year');
            $monthParam = $request->input('month');
            try {
                if ($monthYear) {
                    [$y, $m] = explode('-', $monthYear);
                    $start = Carbon::createFromDate((int)$y, (int)$m, 1)->startOfDay();
                    $end = (clone $start)->endOfMonth()->endOfDay();
                    $q->whereBetween('created_at', [$start, $end]);
                } elseif ($yearParam && $monthParam) {
                    $y = (int)$yearParam; $m = (int)$monthParam;
                    $start = Carbon::createFromDate($y, $m, 1)->startOfDay();
                    $end = (clone $start)->endOfMonth()->endOfDay();
                    $q->whereBetween('created_at', [$start, $end]);
                }
            } catch (\Throwable $e) {
                // ignore invalid month/year
            }
        } elseif ($dateFilter === 'range') {
            $from = $request->input('date_from');
            $to = $request->input('date_to');
            try {
                $fromC = $from ? Carbon::parse($from)->startOfDay() : null;
                $toC = $to ? Carbon::parse($to)->endOfDay() : null;
                if ($fromC && $toC) {
                    $q->whereBetween('created_at', [$fromC, $toC]);
                } elseif ($fromC) {
                    $q->where('created_at', '>=', $fromC);
                } elseif ($toC) {
                    $q->where('created_at', '<=', $toC);
                }
            } catch (\Throwable $e) {
                // ignore invalid dates
            }
        }

        $items = $q->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        if ($request->ajax()) {
            // Return only the list HTML for dynamic updates
            return response()->view('lira.partials.list', compact('items'));
        }
        return view('lira.manage', compact('items'));
    }

    public function exportXlsx(Request $request)
    {
        // Build base query with same filters as index()
        $q = LiraRequest::query();

        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            if ($status === 'awaiting_response') {
                $q->where('status', 'accepted')->whereNull('response_sent_at');
            } else {
                $q->where('status', $status);
            }
        }

        $email = $request->input('email');
        if (!empty($email)) {
            $q->where('email', 'like', '%' . $email . '%');
        }

        // Date filtering (mirror index)
        $dateFilter = $request->input('date_filter');
        $timeframeLabel = 'All dates';
        $start = null; $end = null;
        if ($dateFilter === 'today') {
            $q->whereDate('created_at', Carbon::today());
            $timeframeLabel = Carbon::today()->format('M d, Y');
            $start = Carbon::today()->startOfDay();
            $end = Carbon::today()->endOfDay();
        } elseif ($dateFilter === 'month') {
            // Support either year+month params or legacy month_year
            $year = (int) $request->input('year', (int) date('Y'));
            $month = (int) $request->input('month', (int) date('n'));
            if ($request->filled('month_year')) {
                $parts = explode('-', $request->input('month_year'));
                if (count($parts) === 2) { $year = (int)$parts[0]; $month = (int)$parts[1]; }
            }
            try {
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = (clone $start)->endOfMonth();
                $q->whereBetween('created_at', [$start, $end]);
                $timeframeLabel = $start->format('F Y');
            } catch (\Throwable $e) {
                // ignore invalid month/year
            }
        } elseif ($dateFilter === 'range') {
            $from = $request->input('date_from');
            $to = $request->input('date_to');
            try {
                if ($from) { $start = Carbon::parse($from)->startOfDay(); }
                if ($to) { $end = Carbon::parse($to)->endOfDay(); }
                if ($start && $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                } elseif ($start) {
                    $q->where('created_at', '>=', $start);
                } elseif ($end) {
                    $q->where('created_at', '<=', $end);
                }
                if ($start || $end) {
                    $timeframeLabel = trim(($start? $start->format('M d, Y') : '') . ' – ' . ($end? $end->format('M d, Y') : ''));
                }
            } catch (\Throwable $e) {
                // ignore invalid dates
            }
        }

        $items = $q->orderBy('created_at', 'desc')->get();

        // Helper to map model collection to array rows
        $toRows = function ($collection) {
            $rows = [];
            foreach ($collection as $it) {
                $assist = is_array($it->assistance_types) ? $it->assistance_types : (json_decode($it->assistance_types ?? '[]', true) ?: []);
                $resTypes = is_array($it->resource_types) ? $it->resource_types : (json_decode($it->resource_types ?? '[]', true) ?: []);
                $videos = is_array($it->for_videos) ? $it->for_videos : (json_decode($it->for_videos ?? '[]', true) ?: []);
                $statusText = ($it->status === 'accepted' && !empty($it->response_sent_at)) ? 'Responded' : ($it->status ?? '');
                $rows[] = [
                    'created_at' => optional($it->created_at)->format('Y-m-d H:i:s'),
                    'first_name' => $it->first_name,
                    'middle_name' => $it->middle_name,
                    'last_name' => $it->last_name,
                    'email' => $it->email,
                    'designation' => $it->designation,
                    'department' => $it->department,
                    'action' => $it->action,
                    'assistance_types' => implode(', ', $assist),
                    'resource_types' => implode(', ', $resTypes),
                    'titles_of' => $it->titles_of,
                    'for_borrow_scan' => $it->for_borrow_scan,
                    'for_list' => $it->for_list,
                    'for_videos' => implode(', ', $videos),
                    'status' => $statusText,
                    'processed_at' => $it->processed_at ? Carbon::parse($it->processed_at)->format('Y-m-d H:i:s') : '',
                    'decision_reason' => $it->decision_reason,
                    'response_sent_at' => $it->response_sent_at ? Carbon::parse($it->response_sent_at)->format('Y-m-d H:i:s') : '',
                ];
            }
            return $rows;
        };

        $rows = $toRows($items);

        // Build metadata
        $meta = [
            'status_label' => ($status === 'awaiting_response') ? 'Awaiting Response' : ucfirst((string)($status ?: 'All')),
            'email' => $email,
            'timeframe_label' => $timeframeLabel,
            'start' => $start ? $start->format('M d, Y') : null,
            'end' => $end ? $end->format('M d, Y') : null,
            'generated_at' => Carbon::now()->format('M d, Y H:i'),
        ];

        // If all_tabs=1, generate a workbook with one sheet per status
        if ($request->boolean('all_tabs')) {
            $titles = [
                'All' => null,
                'Pending' => 'pending',
                'Accepted' => 'accepted',
                'Rejected' => 'rejected',
                'Awaiting Response' => 'awaiting_response',
            ];
            $tabbed = [];
            foreach ($titles as $label => $st) {
                $qq = clone $q; // copy the base filtered (date/email) query
                if ($st === 'awaiting_response') {
                    $qq->where('status', 'accepted')->whereNull('response_sent_at');
                } elseif ($st) {
                    $qq->where('status', $st);
                }
                $tabbed[$label] = $toRows($qq->orderBy('created_at', 'desc')->get());
            }
            $filenameSafeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($timeframeLabel ?: 'all'));
            $filename = sprintf('lira_all_tabs_%s.xlsx', $filenameSafeLabel);
            return Excel::download(new LiRAExportAllTabs($meta, $tabbed), $filename);
        }

        $filenameSafeLabel = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($timeframeLabel ?: 'all'));
        $statusSafe = preg_replace('/[^A-Za-z0-9_-]+/', '_', strtolower($meta['status_label']));
        $filename = sprintf('lira_%s_%s.xlsx', $statusSafe, $filenameSafeLabel);

        return Excel::download(new LiRAExport($meta, $rows), $filename);
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
    // Queue the decision email
    Mail::to($lira->email)->queue(new LiraDecision($lira, $lira->status, $lira->decision_reason));

        // Redirect back to the filtered/paginated page if provided
        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            return redirect($returnUrl)->with('status', 'Decision recorded.');
        }
        return redirect()->back()->with('status', 'Decision recorded.');
    }

    // Send a custom response to the requester (post-acceptance)
    public function respond(Request $request, $id)
    {
        $lira = LiraRequest::findOrFail($id);

        // Only allow responding to accepted requests
        if ($lira->status !== 'accepted') {
            return redirect()->back()->with('status', 'Only accepted requests can be responded to.');
        }
        // Prevent duplicate response unless explicitly allowed in future
        if (!empty($lira->response_sent_at)) {
            return redirect()->back()->with('status', 'A response has already been sent for this request.');
        }

        $validated = $request->validate([
            'response_subject' => 'required|string|max:255',
            'response_message' => 'required|string|max:10000',
        ]);

        // Send email to requester
        try {
            // Queue the post-acceptance response email
            Mail::to($lira->email)->queue(new \App\Mail\LiraResponse($lira, $validated['response_subject'], $validated['response_message']));
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', 'Failed to send email: '.$e->getMessage());
        }

        // Record response metadata
        $lira->response_subject = $validated['response_subject'];
        $lira->response_message = $validated['response_message'];
        $lira->response_sent_at = now();
        $lira->responded_by = Auth::id();
        $lira->save();

        // Redirect back to the filtered/paginated page if provided
        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            return redirect($returnUrl)->with('status', 'Response sent to requester.');
        }
        return redirect()->back()->with('status', 'Response sent to requester.');
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
