<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * ALINET Public Form Controller
 * 
 * Handles the public-facing ALINET appointment request form for external users
 * who need temporary library access (online or onsite).
 * 
 * @package App\Http\Controllers
 */
class AlinetController extends Controller
{
    /**
     * Display the ALINET appointment request form
     * 
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('alinet.form');
    }

    /**
     * Process the ALINET appointment request form submission
     * 
     * Validates input based on mode (onsite requires more fields than online),
     * calculates appointment date for onsite requests, and sends email
     * notification to library staff.
     * 
     * @param Request $request HTTP request with form data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForm(Request $request)
    {
        // Base validation rules
        $rules = [
            'prefix' => 'nullable|string|max:10',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mode_of_research' => 'required|string|in:Online (Virtual),Onsite (Saturday 8:00am3:00pm)',
            'strand_course' => 'nullable|string|max:255',
            'institution_college' => 'required|string|max:255',
        ];

        // Conditionally require titles, assistance, and resources only for Onsite mode
        if (stripos($request->input('mode_of_research', ''), 'Onsite') !== false) {
            $rules['titles_or_topics'] = 'required|string';
            $rules['assistance'] = 'required|array|min:1';
            $rules['assistance.*'] = 'string';
            $rules['resource_types'] = 'required|array|min:1';
            $rules['resource_types.*'] = 'string';
        } else {
            // Optional for Online mode
            $rules['titles_or_topics'] = 'nullable|string';
            $rules['assistance'] = 'nullable|array';
            $rules['assistance.*'] = 'string';
            $rules['resource_types'] = 'nullable|array';
            $rules['resource_types.*'] = 'string';
        }

        $validated = $request->validate($rules);

        // Compute appointment_date for Onsite; Online has no appointment date
        $appointmentDate = null;
    if (stripos($validated['mode_of_research'] ?? '', 'Onsite') !== false) {
            // Determine Saturday of the week using the application timezone
            // dayOfWeek: 0=Sun, 1=Mon, ..., 6=Sat
            $tz = 'Asia/Manila';
            $today = \Carbon\Carbon::now($tz)->startOfDay();
            $dow = (int) $today->dayOfWeek;
            $daysAhead = ($dow === 0) ? 6 : (6 - $dow); // Sunday -> +6 (next Sat), Mon..Sat -> 6 - dow
            $appointmentDate = $today->copy()->addDays($daysAhead)->toDateString();
        }

        $appointment = AlinetAppointment::create(array_merge($validated, [
            'appointment_date' => $appointmentDate,
        ]));

        // Send email to library Gmail
        Mail::send('alinet.email', ['appointment' => $appointment], function ($message) {
            $message->to(env('ALINET_LIBRARIAN_EMAIL'))
                ->subject('New ALINET Appointment Request');
        });

        return redirect()->route('alinet.form')->with('success', 'Appointment request submitted!');
    }
}
