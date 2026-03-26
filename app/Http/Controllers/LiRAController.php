<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\LiraRequest;
use App\Models\Catalog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\LiraSubmitted;
use App\Mail\LiraDecision;
use App\Models\CartItem;
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
        $catalogId = is_numeric($request->query('catalog_id')) ? (int) $request->query('catalog_id') : null;

        // Resolve canonical catalog details by ID when available.
        if (!empty($catalogId)) {
            $catalog = Catalog::find($catalogId);
            if ($catalog) {
                $title = (string) ($catalog->title ?? '');
                $author = (string) ($catalog->author ?? '');
                $call_number = (string) ($catalog->call_number ?? '');
                $isbn = (string) ($catalog->isbn ?? '');
                $lccn = (string) ($catalog->lccn ?? '');
                $issn = (string) ($catalog->issn ?? '');
            }
        }

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

        $forBorrowScan = $this->formatBorrowScanLine($title, $author, $call_number);

        $fromCart = (int) $request->query('from_cart', 0) === 1;
        $checkoutToken = trim((string) $request->query('checkout_token', ''));
        if ($fromCart) {
            $cartEntries = session('lira_cart_checkout_maps.' . $checkoutToken, []);
            if (empty($checkoutToken) || !is_array($cartEntries) || count($cartEntries) === 0) {
                return redirect()->route('cart.index')->with('error', 'Your checkout session expired. Please checkout again from My Cart.');
            }

            $forBorrowScan = $this->buildBorrowScanDisplayFromEntries($cartEntries);
        }

        $cancelUrl = $this->resolveCancelUrl($request, $catalogId, $fromCart);

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
            'catalog_id' => $catalogId,
            'for_borrow_scan' => $forBorrowScan,
            'from_cart' => $fromCart ? 1 : 0,
            'checkout_token' => $checkoutToken,
            'cancel_url' => $cancelUrl,
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
            'department' => 'required|in:Grade School,Junior High,Senior High,College,Graduate School',
            'action' => 'nullable|in:borrow,scanning',
            'catalog_id' => 'nullable|integer|exists:catalogs,id',
            'from_cart' => 'nullable|integer|in:0,1',
            'checkout_token' => 'nullable|string|max:128',
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

        $fromCart = (int) $request->input('from_cart', 0) === 1;
        $checkoutToken = trim((string) $request->input('checkout_token', ''));

        $assistanceNeedsBorrowScan = in_array('Book Borrowing', $assistance, true) || in_array('Library Scanning', $assistance, true);
        if ($assistanceNeedsBorrowScan && !$fromCart && !$request->filled('catalog_id')) {
            return redirect()->back()->withErrors([
                'for_borrow_scan' => 'Please start your request from a Catalog item or via My Cart checkout so title, author, and call number are mapped automatically.'
            ])->withInput();
        }

        $basePayload = [
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
            'for_list' => $request->input('for_list'),
            'for_videos' => json_encode($videos),
        ];

        $createdRequests = [];

        if ($fromCart) {
            $cartEntries = session('lira_cart_checkout_maps.' . $checkoutToken, []);
            if (empty($checkoutToken) || !is_array($cartEntries) || count($cartEntries) === 0) {
                return redirect()->route('cart.index')->with('error', 'Your checkout session expired. Please checkout again from My Cart.');
            }

            foreach ($cartEntries as $entry) {
                $catalogId = isset($entry['catalog_id']) && is_numeric($entry['catalog_id']) ? (int) $entry['catalog_id'] : null;
                $title = trim((string) ($entry['title'] ?? ''));
                $author = trim((string) ($entry['author'] ?? ''));
                $callNumber = trim((string) ($entry['call_number'] ?? ''));
                $forBorrowScan = $this->formatBorrowScanLine($title, $author, $callNumber);

                $createdRequests[] = LiraRequest::create(array_merge($basePayload, [
                    'catalog_id' => $catalogId,
                    'for_borrow_scan' => $forBorrowScan,
                ]));
            }

            $user = Auth::user();
            $sf = $user?->studentFaculty;
            if ($sf) {
                $cartItemIds = collect($cartEntries)
                    ->pluck('cart_item_id')
                    ->filter(fn ($id) => is_numeric($id))
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                if (!empty($cartItemIds)) {
                    CartItem::where('student_faculty_id', $sf->id)
                        ->whereIn('id', $cartItemIds)
                        ->delete();
                }
            }

            session()->forget('lira_cart_checkout_maps.' . $checkoutToken);
        } else {
            $catalog = null;
            if ($request->filled('catalog_id')) {
                $catalog = Catalog::find($request->integer('catalog_id'));
            }

            $forBorrowScan = null;
            if ($catalog) {
                $forBorrowScan = $this->formatBorrowScanLine(
                    (string) ($catalog->title ?? ''),
                    (string) ($catalog->author ?? ''),
                    (string) ($catalog->call_number ?? '')
                );
            }

            $createdRequests[] = LiraRequest::create(array_merge($basePayload, [
                'catalog_id' => $catalog?->id,
                'for_borrow_scan' => $forBorrowScan,
            ]));
        }

        // Send notification to librarian/staff
        $librarian = env('ALINET_LIBRARIAN_EMAIL', null);
        if ($librarian) {
            foreach ($createdRequests as $createdRequest) {
                // Queue the email to avoid blocking the request
                Mail::to($librarian)->queue(new LiraSubmitted($createdRequest));
            }
        }

        $count = count($createdRequests);
        $statusMessage = $count > 1
            ? 'Your requests were submitted successfully (' . $count . ' records). Thank you!'
            : 'Your request was submitted. Thank you!';

        return redirect()->route('lira.form')->with('status', $statusMessage);
    }

    private function resolveCancelUrl(Request $request, ?int $catalogId, bool $fromCart): string
    {
        $candidate = trim((string) $request->query('return_to', ''));
        if ($candidate === '') {
            $candidate = trim((string) url()->previous());
        }

        $safe = $this->sanitizeLocalReturnUrl($candidate);
        if ($safe !== null) {
            return $safe;
        }

        if ($fromCart) {
            return route('cart.index');
        }

        if (!empty($catalogId)) {
            return route('catalogs.show', $catalogId);
        }

        return route('alert-services.index');
    }

    private function sanitizeLocalReturnUrl(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        $parts = parse_url($url);
        if ($parts === false) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '/');
        if ($path === '') {
            $path = '/';
        }

        if (str_starts_with($path, '/lira/form') || str_starts_with($path, '/lira/jotform')) {
            return null;
        }

        if (isset($parts['host'])) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            if (!$appHost || strcasecmp((string) $parts['host'], (string) $appHost) !== 0) {
                return null;
            }
        }

        $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

        return $path . $query . $fragment;
    }

    private function formatBorrowScanLine(string $title, string $author, string $callNumber): string
    {
        $parts = [];
        $title = trim($title);
        $author = trim($author);
        $callNumber = trim($callNumber);

        if ($title !== '') {
            $parts[] = $title;
        }
        if ($author !== '') {
            $parts[] = $author;
        }
        if ($callNumber !== '') {
            $parts[] = $callNumber;
        }

        return implode(', ', $parts);
    }

    private function buildBorrowScanDisplayFromEntries(array $entries): string
    {
        $lines = [];
        foreach ($entries as $entry) {
            $title = e(trim((string) ($entry['title'] ?? '')));
            $author = e(trim((string) ($entry['author'] ?? '')));
            $call = e(trim((string) ($entry['call_number'] ?? '')));
            $parts = [];
            if ($title !== '') {
                $parts[] = '<strong>' . $title . '</strong>';
            }
            if ($author !== '') {
                $parts[] = $author;
            }
            if ($call !== '') {
                $parts[] = $call;
            }
            if (!empty($parts)) {
                $lines[] = '<li>' . implode(', ', $parts) . '</li>';
            }
        }

        return '<ol>' . implode('', $lines) . '</ol>';
    }

    // Admin listing and filtering
    public function index(Request $request)
    {
        $q = LiraRequest::query()->with(['catalog:id,title,copies_count,borrowed_count']);
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'awaiting_response') {
                // Accepted but not yet responded
                $q->where('status', 'accepted')->whereNull('response_sent_at');
            } elseif ($status === 'borrowed' || $status === 'returned') {
                $q->where('loan_status', $status);
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
            } elseif ($status === 'borrowed' || $status === 'returned') {
                $q->where('loan_status', $status);
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
                if ($it->loan_status === 'borrowed') {
                    $statusText = 'Borrowed';
                } elseif ($it->loan_status === 'returned') {
                    $statusText = 'Returned';
                }
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
                    'loan_status' => $it->loan_status,
                    'processed_at' => $it->processed_at ? Carbon::parse($it->processed_at)->format('Y-m-d H:i:s') : '',
                    'decision_reason' => $it->decision_reason,
                    'response_sent_at' => $it->response_sent_at ? Carbon::parse($it->response_sent_at)->format('Y-m-d H:i:s') : '',
                    'borrowed_at' => $it->borrowed_at ? Carbon::parse($it->borrowed_at)->format('Y-m-d H:i:s') : '',
                    'returned_at' => $it->returned_at ? Carbon::parse($it->returned_at)->format('Y-m-d H:i:s') : '',
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
                'Borrowed' => 'borrowed',
                'Returned' => 'returned',
            ];
            $tabbed = [];
            foreach ($titles as $label => $st) {
                $qq = clone $q; // copy the base filtered (date/email) query
                if ($st === 'awaiting_response') {
                    $qq->where('status', 'accepted')->whereNull('response_sent_at');
                } elseif ($st === 'borrowed' || $st === 'returned') {
                    $qq->where('loan_status', $st);
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

        $successMessage = $lira->status === 'accepted' 
            ? 'Request accepted successfully. An email notification has been sent to the requester.' 
            : 'Request rejected successfully. An email notification has been sent to the requester.';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        // Redirect back to the filtered/paginated page if provided
        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            return redirect($returnUrl)->with('status', $successMessage);
        }
        return redirect()->back()->with('status', $successMessage);
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

        $catalogForCheck = null;
        if ($lira->action === 'borrow' && !empty($lira->catalog_id)) {
            $catalogForCheck = Catalog::find($lira->catalog_id);
        }

        $rules = [
            'response_subject' => 'required|string|max:255',
            'response_message' => 'required|string|max:10000',
        ];
        if ($catalogForCheck && is_null($catalogForCheck->copies_count)) {
            $rules['manual_copy_check_confirmed'] = 'accepted';
        }
        $validated = $request->validate($rules, [
            'manual_copy_check_confirmed.accepted' => 'Manual library copy verification is required for this catalog before sending a response.',
        ]);

        // Send email to requester
        try {
            // Queue the post-acceptance response email
            Mail::to($lira->email)->queue(new \App\Mail\LiraResponse($lira, $validated['response_subject'], $validated['response_message']));
        } catch (\Throwable $e) {
            return redirect()->back()->with('status', 'Failed to send email: '.$e->getMessage());
        }

        try {
            DB::transaction(function () use ($lira, $validated) {
                // Refresh and lock request row to avoid double-borrow race.
                $lockedLira = LiraRequest::whereKey($lira->id)->lockForUpdate()->firstOrFail();

                // Record response metadata.
                $lockedLira->response_subject = $validated['response_subject'];
                $lockedLira->response_message = $validated['response_message'];
                $lockedLira->response_sent_at = now();
                $lockedLira->responded_by = Auth::id();

                // For borrow requests tied to a catalog, mark as borrowed and increment inventory usage.
                if ($lockedLira->action === 'borrow' && !empty($lockedLira->catalog_id)) {
                    $catalog = Catalog::whereKey($lockedLira->catalog_id)->lockForUpdate()->first();
                    if ($catalog) {
                        $totalCopies = is_null($catalog->copies_count) ? null : (int) $catalog->copies_count;
                        $borrowedCount = (int) ($catalog->borrowed_count ?? 0);

                        if (!is_null($totalCopies) && $borrowedCount >= $totalCopies) {
                            throw new \RuntimeException('Cannot mark as borrowed: all copies are already borrowed.');
                        }

                        $catalog->borrowed_count = $borrowedCount + 1;
                        $catalog->save();

                        $lockedLira->loan_status = 'borrowed';
                        $lockedLira->borrowed_at = now();
                        $lockedLira->borrowed_by = Auth::id();
                        $lockedLira->returned_at = null;
                        $lockedLira->returned_by = null;
                    }
                }

                $lockedLira->save();
            });
        } catch (\Throwable $e) {
            $message = $e->getMessage() ?: 'Failed to update circulation status.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('status', $message);
        }

        $successMessage = 'Response sent successfully to the requester.';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        // Redirect back to the filtered/paginated page if provided
        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            return redirect($returnUrl)->with('status', $successMessage);
        }
        return redirect()->back()->with('status', $successMessage);
    }

    public function markReturned(Request $request, $id)
    {
        $lira = LiraRequest::findOrFail($id);

        if ($lira->loan_status !== 'borrowed') {
            $message = 'Only borrowed requests can be marked as returned.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('status', $message);
        }

        DB::transaction(function () use ($lira) {
            $lockedLira = LiraRequest::whereKey($lira->id)->lockForUpdate()->firstOrFail();
            if ($lockedLira->loan_status !== 'borrowed') {
                return;
            }

            if (!empty($lockedLira->catalog_id)) {
                $catalog = Catalog::whereKey($lockedLira->catalog_id)->lockForUpdate()->first();
                if ($catalog) {
                    $catalog->borrowed_count = max(((int) ($catalog->borrowed_count ?? 0)) - 1, 0);
                    $catalog->save();
                }
            }

            $lockedLira->loan_status = 'returned';
            $lockedLira->returned_at = now();
            $lockedLira->returned_by = Auth::id();
            $lockedLira->save();
        });

        $successMessage = 'Request marked as returned and inventory updated.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        $returnUrl = $request->input('return_url');
        if ($returnUrl) {
            return redirect($returnUrl)->with('status', $successMessage);
        }

        return redirect()->back()->with('status', $successMessage);
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
            return response()->json(['success' => true, 'message' => 'LiRA request deleted successfully.', 'id' => $id]);
        }

        return redirect()->route('lira.manage')->with('status', 'LiRA request deleted successfully.');
    }
}
