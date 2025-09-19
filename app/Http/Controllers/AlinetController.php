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
            'strand_course' => 'nullable|string|max:255',
            'institution_college' => 'nullable|string|max:255',
            'appointment_date' => 'required|date|after_or_equal:today',
            'services' => 'required|array|min:1',
            'services.*' => 'string',
        ]);

        $appointment = AlinetAppointment::create($validated);

        // Send email to librarian's Gmail
        Mail::send('alinet.email', ['appointment' => $appointment], function ($message) {
            $message->to(env('ALINET_LIBRARIAN_EMAIL'))
                ->subject('New ALINET Appointment Request');
        });

        return redirect()->route('alinet.form')->with('success', 'Appointment request submitted!');
    }
}
