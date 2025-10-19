<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AlinetController extends Controller
{
    public function showForm()
    {
        return view('alinet.form');
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'nullable|string|max:10',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mode_of_research' => 'required|string|in:Online (Virtual),Onsite (Saturday 8:00am–3:00pm)',
            'strand_course' => 'nullable|string|max:255',
            'institution_college' => 'nullable|string|max:255',
            'titles_or_topics' => 'required|string',
            'assistance' => 'required|array|min:1',
            'assistance.*' => 'string',
            'resource_types' => 'required|array|min:1',
            'resource_types.*' => 'string',
        ]);

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

        // Send email to librarian's Gmail
        Mail::send('alinet.email', ['appointment' => $appointment], function ($message) {
            $message->to(env('ALINET_LIBRARIAN_EMAIL'))
                ->subject('New ALINET Appointment Request');
        });

        return redirect()->route('alinet.form')->with('success', 'Appointment request submitted!');
    }
}
