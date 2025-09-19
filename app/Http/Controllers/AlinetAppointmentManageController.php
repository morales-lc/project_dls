<?php

namespace App\Http\Controllers;

use App\Models\AlinetAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        ]);
        $appointment = AlinetAppointment::findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        // Send email to requester
        $emailView = $request->status === 'accepted' ? 'alinet.email_accepted' : 'alinet.email_rejected';
        Mail::send($emailView, ['appointment' => $appointment], function ($message) use ($appointment) {
            $message->to($appointment->email)
                ->subject('ALINET Appointment ' . ucfirst($appointment->status));
        });

        return redirect()->route('alinet.manage')->with('success', 'Appointment status updated and email sent.');
    }
}
