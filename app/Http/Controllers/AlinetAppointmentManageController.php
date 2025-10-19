<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlinetAppointmentAccepted;
use App\Mail\AlinetAppointmentRejected;

class AlinetAppointmentManageController extends Controller
{
    public function index()
    {
        $status = request('status');
        $q = request('q');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $service = request('service');
        $perPage = (int) request('per_page', 15);
        if ($perPage < 5) { $perPage = 5; }
        if ($perPage > 100) { $perPage = 100; }

        $appointments = AlinetAppointment::query()
            ->status($status)
            ->service($service)
            ->dateBetween($dateFrom, $dateTo)
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->appends(request()->query());

        return view('alinet.manage', compact('appointments', 'status', 'q', 'dateFrom', 'dateTo', 'service', 'perPage'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'reason' => 'nullable|string|max:5000',
        ]);
        $appointment = AlinetAppointment::findOrFail($id);
        $appointment->status = $request->status;

        // If accepting an Onsite request, set appointment_date to the upcoming Saturday (next Saturday if today is Sunday)
    if ($request->status === 'accepted' && (stripos($appointment->mode_of_research, 'Onsite') !== false)) {
            $tz = 'Asia/Manila';
            $today = \Carbon\Carbon::now($tz)->startOfDay();
            $dow = (int) $today->dayOfWeek; // 0=Sun..6=Sat
            $daysAhead = ($dow === 0) ? 6 : (6 - $dow);
            $appointment->appointment_date = $today->copy()->addDays($daysAhead)->toDateString();
        }
        $appointment->save();

        // Send email to requester using Mailable classes
        if ($request->status === 'accepted') {
            Mail::to($appointment->email)->send(new AlinetAppointmentAccepted($appointment));
        } else {
            Mail::to($appointment->email)->send(new AlinetAppointmentRejected($appointment, $request->input('reason')));
        }

        return redirect()->route('alinet.manage')->with('success', 'Appointment status updated and email sent.');
    }
}
